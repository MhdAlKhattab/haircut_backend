<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Employee;
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

        if ($request['products'])
            $order->Products()->attach($request['products']);
        if ($request['services'])
            $order->Services()->attach($request['services']);

        return response()->json(['data' => $order,
         'products' => $request['products'], 'services' => $request['services']], 200);
    }

    public function getOrders($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Orders, 200);
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

        if ($request['products'])
            $order->Products()->sync($request['products']);
        if ($request['services'])
            $order->Services()->sync($request['services']);
        
        $order->save();

        return response()->json(['data' => $order,
        'products' => $request['products'], 'services' => $request['services']], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);

        if(!$order){
            return response()->json(['errors' => 'There is no order with this id !'], 400);
        }

        $order->delete();
        return response()->json(['message' => "Order Deleted"], 200);
    }
}
