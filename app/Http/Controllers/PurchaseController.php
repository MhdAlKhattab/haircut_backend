<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Purchase;
use App\Models\Branch;

class PurchaseController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'supplier_id' => 'required|numeric|exists:suppliers,id',
            'products' => 'array|min:1',
            'products.*' => 'numeric|exists:products,id',
            'products_count' => 'array',
            'products_count.*' => 'numeric|min:1',
            'sundrys' => 'array|min:1',
            'sundrys.*' => 'numeric|exists:services,id',
            'amount' => 'required|numeric',
            'discount' => 'numeric|min:1|max:100',
            'type' => 'required|string|in:product,sundry',
        ]);
    }

    public function addPurchase(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        if (!$request['products'] and !$request['sundrys'])
            return response()->json(['errors' => 'You must send products or sundry products !'], 400);
        if ($request['type'] == 'product' and !$request['products'])
            return response()->json(['errors' => 'There are no products!'], 400);
        if ($request['type'] == 'sundry' and !$request['sundrys'])
            return response()->json(['errors' => 'There are no sundry products!'], 400);
        if ($request['products'] and !$request['products_count']) 
            return response()->json(['errors' => 'There are no products count!'], 400);
        if ($request['products'] and count($request['products_count']) != count($request['products'])) 
            return response()->json(['errors' => 'The size of count array does not equal product array!'], 400);

        // Initial
        $amount_after_discount = $request['amount'];

        $purchase = new Purchase;

        $purchase->branch_id = $request['branch_id'];
        $purchase->supplier_id = $request['supplier_id'];
        $purchase->amount = $request['amount'];
        $purchase->type = $request['type'];

        if ($request['discount']){
            $purchase->discount = $request['discount'];

            $discounted_amount = ($request['amount'] * $request['discount']) / 100.0;
            $amount_after_discount = $request['amount'] - $discounted_amount;
        }

        $purchase->amount_after_discount = $amount_after_discount;

        $purchase->tax = ($amount_after_discount * 15) / 100.0;

        $purchase->save();

        if ($request['type'] == 'product'){
            $productsWithPivot = [];

            for ($i = 0; $i < count($request['products']); $i+=1) {
                $productsWithPivot[$request['products'][$i]] = ['quantity' => $request['products_count'][$i]];
            }

            $purchase->Products()->attach($productsWithPivot);
        }else
            $purchase->Sundry_Products()->attach($request['sundrys']);

        return response()->json(['data' => $purchase,
                                'products' => $request['products'],
                                'products_count' => $request['products_count'],
                                'sundrys' => $request['sundrys']], 200);
    }

    public function getPurchases($branch_id)
    {
        $purchases = Purchase::where('branch_id', '=', $branch_id)
                        ->with(['Supplier:id,name',
                                'products:id,name',
                                'sundry_products:id,name'])
                        ->get();

        return response()->json($purchases, 200);
    }

    public function updatePurchase(Request $request, $id)
    {
        $purchase = Purchase::find($id);

        if(!$purchase){
            return response()->json(['errors' => 'There is no purchase with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'supplier_id' => 'numeric|exists:suppliers,id',
                'products' => 'array|min:1',
                'products.*' => 'numeric|exists:products,id',
                'products_count' => 'array',
                'products_count.*' => 'numeric|min:1',
                'sundrys' => 'array|min:1',
                'sundrys.*' => 'numeric|exists:services,id',
                'amount' => 'numeric',
                'discount' => 'min:1|max:100',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($purchase->type == 'product' and $request['sundrys'])
            return response()->json(['errors' => 'You can not send sundry products, The type of purchase is product!'], 400);
        if($purchase->type == 'sundry' and $request['products'])
            return response()->json(['errors' => 'You can not send products, The type of purchase is sundry product!'], 400);
        if ($request['products'] and !$request['products_count']) 
            return response()->json(['errors' => 'There are no products count!'], 400);
        if ($request['products'] and count($request['products_count']) != count($request['products'])) 
            return response()->json(['errors' => 'The size of count array does not equal product array!'], 400);

        if($request['supplier_id'])
            $purchase->supplier_id = $request['supplier_id'];
        if($request['amount'])
            $purchase->amount = $request['amount'];
        if($request['discount'])
            $purchase->discount = $request['discount'];

        // Get Discounted Amount
        $discounted_amount = ($purchase->amount * $purchase->discount) / 100.0;
        $amount_after_discount = $purchase->amount - $discounted_amount;

        $purchase->amount_after_discount = $amount_after_discount;
        $purchase->tax = ($amount_after_discount * 15) / 100.0;

        $purchase->save();

        if ($request['products']){
            $productsWithPivot = [];

            for ($i = 0; $i < count($request['products']); $i+=1) {
                $productsWithPivot[$request['products'][$i]] = ['quantity' => $request['products_count'][$i]];
            }

            $purchase->Products()->sync($productsWithPivot);
        }else
            $purchase->Sundry_Products()->sync($request['sundrys']);

        return response()->json(['data' => $purchase,
                                'products' => $request['products'],
                                'products_count' => $request['products_count'],
                                'sundrys' => $request['sundrys']], 200);
    }

    public function deletePurchase($id)
    {
        $purchase = Purchase::find($id);

        if(!$purchase){
            return response()->json(['errors' => 'There is no purchase with this id !'], 400);
        }

        $purchase->delete();
        return response()->json(['message' => "Purchase Deleted"], 200);
    }
}
