<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sundry_Product;
use App\Models\Branch;

class SundryProductController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);
    }

    public function addSundryProduct(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $sundry_product = new Sundry_Product;

        $sundry_product->branch_id = $request['branch_id'];
        $sundry_product->name = $request['name'];
        $sundry_product->price = $request['price'];

        $sundry_product->save();

        return response()->json(['data' => $sundry_product], 200);
    }

    public function getSundryProducts($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Sundry_Products, 200);
    }

    public function updateSundryProduct(Request $request, $id)
    {
        $sundry_product = Sundry_Product::find($id);

        if(!$sundry_product){
            return response()->json(['errors' => 'There is no sundry product with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'price' => 'numeric',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $sundry_product->name = $request['name'];
        if($request['price'])
            $sundry_product->price = $request['price'];
        
        $sundry_product->save();

        return response()->json(['data' => $sundry_product], 200);
    }

    public function deleteSundryProduct($id)
    {
        $sundry_product = Sundry_Product::find($id);

        if(!$sundry_product){
            return response()->json(['errors' => 'There is no sundry product with this id !'], 400);
        }

        $sundry_product->delete();
        
        return response()->json(['message' => "Sundry Product Deleted"], 200);
    }
}
