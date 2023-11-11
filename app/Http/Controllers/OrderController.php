<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\Employee;
use App\Models\Employee_Info;
use App\Models\Branch;

class OrderController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'employee_id' => 'required|numeric|exists:employees,id',
            'customer_id' => 'required|numeric|exists:customers,id',
            'products' => 'array|min:1',
            'products.*' => 'numeric|exists:products,id',
            'products_count' => 'array',
            'products_count.*' => 'numeric|min:1',
            'services' => 'array|min:1',
            'services.*' => 'numeric|exists:services,id',
            'amount' => 'required|numeric',
            'amount_pay_type' => 'required|string|in:cash,online',
            'discount' => 'numeric|min:1|max:100',
            'tip' => 'numeric',
            'tip_pay_type' => 'string|in:cash,online',
        ]);
    }

    public function addOrder(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        if (!$request['products'] and !$request['services'])
            return response()->json(['errors' => 'You must send products or services !'], 400);
        if ($request['products'] and !$request['products_count']) 
            return response()->json(['errors' => 'There are no products count!'], 400);
        if ($request['products'] and count($request['products_count']) != count($request['products'])) 
            return response()->json(['errors' => 'The size of count array does not equal product array!'], 400);
        if ($request['products']){
            for ($i = 0; $i < count($request['products']); $i+=1){
                $product = Product::find($request['products'][$i]);
                if ($request['products_count'][$i] > $product->quantity)
                    return response()->json(['errors' => 'There is no enough products!'], 400);
            }
        }

        // Initial
        $amount_after_discount = $request['amount'];

        // Get User's Commission
        $employee_commission = Employee::find($request['employee_id'])->commission;
        $manager_commission = 0;
        $representative_commission = 0;

        $order = new Order;

        $order->branch_id = $request['branch_id'];
        $order->employee_id = $request['employee_id'];
        $order->customer_id = $request['customer_id'];
        $order->amount = $request['amount'];
        $order->amount_pay_type = $request['amount_pay_type'];

        if ($request['discount']){
            $order->discount = $request['discount'];

            $discounted_amount = ($request['amount'] * $request['discount']) / 100.0;
            $amount_after_discount = $request['amount'] - $discounted_amount;
        }

        $order->amount_after_discount = $amount_after_discount;

        if ($request['tip']){
            $order->tip = $request['tip'];
            $order->tip_pay_type = $request['tip_pay_type'];
        }

        $order->tax = ($amount_after_discount * 15) / 100.0;
        $order->employee_commission = ($amount_after_discount * $employee_commission) / 100.0;
        $order->manager_commission = ($amount_after_discount * $manager_commission) / 100.0;
        $order->representative_commission = ($amount_after_discount * $representative_commission) / 100.0;

        $order->save();

        if ($request['products']){
            $productsWithPivot = [];

            for ($i = 0; $i < count($request['products']); $i+=1) {
                $product = Product::find($request['products'][$i]);
                $product->quantity -= $request['products_count'][$i];
                $product->save();

                $productsWithPivot[$request['products'][$i]] = ['quantity' => $request['products_count'][$i]];
            }

            $order->Products()->attach($productsWithPivot);
        }
        if ($request['services'])
            $order->Services()->attach($request['services']);

        // Get Employee Information
        $employee_info = Employee_Info::where('employee_id', '=', $request['employee_id'])->first();
        $employee_info->total_order += 1;
        $employee_info->total_revenue += $order->amount_after_discount;
        $employee_info->total_commission += $order->employee_commission;
        $employee_info->save();

        return response()->json(['data' => $order,
                                'products' => $request['products'],
                                'products_count' => $request['products_count'],
                                'services' => $request['services']], 200);
    }

    public function getOrders($branch_id)
    {
        $orders = Order::where('branch_id', '=', $branch_id)
                        ->with(['Employee:id,name',
                                'Customer:id,name,phone_number',
                                'services:id,name',
                                'products:id,name'])
                        ->get();

        return response()->json($orders, 200);
    }

    public function getDailyReport($branch_id)
    {
        $daily_orders = DB::table('orders as o')
                ->select(array(DB::Raw('count(o.id) as Total_Orders'),
                            DB::Raw('sum(o.amount_after_discount) as Total_Revenues'),
                            DB::Raw('sum(o.employee_commission) as Total_Commissions'),
                            DB::Raw('DATE(o.created_at) as day')))
                ->groupBy('day')
                ->orderBy('o.created_at', 'DESC')
                ->get();

        return response()->json($daily_orders, 200);
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::find($id);

        if(!$order){
            return response()->json(['errors' => 'There is no order with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'numeric|exists:employees,id',
                'customer_id' => 'numeric|exists:customers,id',
                'products' => 'array|min:1',
                'products.*' => 'numeric|exists:products,id',
                'products_count' => 'array',
                'products_count.*' => 'numeric|min:1',
                'services' => 'array|min:1',
                'services.*' => 'numeric|exists:services,id',
                'amount' => 'numeric',
                'amount_pay_type' => 'string|in:cash,online',
                'discount' => 'min:1|max:100',
                'tip' => 'numeric',
                'tip_pay_type' => 'string|in:cash,online',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if ($request['products'] and !$request['products_count']) 
            return response()->json(['errors' => 'There are no products count!'], 400);
        if ($request['products'] and count($request['products_count']) != count($request['products'])) 
            return response()->json(['errors' => 'The size of count array does not equal product array!'], 400);
        if ($request['products']){
            $products = $order->Products;
            for ($i = 0; $i < count($products); $i+=1){
                $product = Product::find($products[$i]->id);
                $product->quantity += $products[$i]->pivot->quantity;
                $product->save();
            }

            for ($i = 0; $i < count($request['products']); $i+=1){
                $product = Product::find($request['products'][$i]);

                if ($request['products_count'][$i] > $product->quantity){
                    for ($i = 0; $i < count($products); $i+=1){
                        $product = Product::find($products[$i]->id);
                        $product->quantity -= $products[$i]->pivot->quantity;
                        $product->save();
                    }
                    return response()->json(['errors' => 'There is no enough products!'], 400);
                }
            }
        }

        if($request['employee_id'])
            $order->employee_id = $request['employee_id'];
        if($request['customer_id'])
            $order->customer_id = $request['customer_id'];
        if($request['amount'])
            $order->amount = $request['amount'];
        if($request['amount_pay_type'])
            $order->amount_pay_type = $request['amount_pay_type'];
        if($request['discount'])
            $order->discount = $request['discount'];
        if($request['tip'])
            $order->tip = $request['tip'];
        if($request['tip_pay_type'])
            $order->tip_pay_type = $request['tip_pay_type'];

        // Get Employee Information
        $employee_info = Employee_Info::where('employee_id', '=', $order->employee_id)->first();
        $employee_info->total_commission -= $order->employee_commission;
        $employee_info->total_revenue -= $order->amount_after_discount;

        // Get Discounted Amount
        $discounted_amount = ($order->amount * $order->discount) / 100.0;
        $amount_after_discount = $order->amount - $discounted_amount;

        // Get User's Commission
        $employee_commission = Employee::find($order->employee_id)->commission;
        $manager_commission = 0;
        $representative_commission = 0;

        $order->amount_after_discount = $amount_after_discount;
        $order->tax = ($amount_after_discount * 15) / 100.0;
        $order->employee_commission = ($amount_after_discount * $employee_commission) / 100.0;
        $order->manager_commission = ($amount_after_discount * $manager_commission) / 100.0;
        $order->representative_commission = ($amount_after_discount * $representative_commission) / 100.0;

        $order->save();

        $employee_info->total_commission += $order->employee_commission;
        $employee_info->total_revenue += $order->amount_after_discount;
        $employee_info->save();

        if ($request['products']){
            $productsWithPivot = [];

            for ($i = 0; $i < count($request['products']); $i+=1) {
                $product = Product::find($request['products'][$i]);
                $product->quantity -= $request['products_count'][$i];
                $product->save();

                $productsWithPivot[$request['products'][$i]] = ['quantity' => $request['products_count'][$i]];
            }

            $order->Products()->sync($productsWithPivot);
        }
        if ($request['services'])
            $order->Services()->sync($request['services']);
        
        return response()->json(['data' => $order,
                                'products' => $request['products'],
                                'products_count' => $request['products_count'],
                                'services' => $request['services']], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);

        if(!$order){
            return response()->json(['errors' => 'There is no order with this id !'], 400);
        }

        // Get Employee Information
        $employee_info = Employee_Info::where('employee_id', '=', $order->employee_id)->first();
        $employee_info->total_order -= 1;
        $employee_info->total_revenue -= $order->amount_after_discount;
        $employee_info->total_commission -= $order->employee_commission;
        $employee_info->save();

        $products = $order->Products;
        for ($i = 0; $i < count($products); $i+=1){
            $product = Product::find($products[$i]->id);
            $product->quantity += $products[$i]->pivot->quantity;
            $product->save();
        }

        $order->delete();
        return response()->json(['message' => "Order Deleted"], 200);
    }
}
