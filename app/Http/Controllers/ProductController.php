<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Branch;

class ProductController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'purchasing_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'image' => ['required', 'image','mimes:jpeg,jpg,png'],
            'quantity' => 'integer',
        ]);
    }

    public function addProduct(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $product = new Product;

        $product->branch_id = $request['branch_id'];
        $product->name = $request['name'];
        $product->purchasing_price = $request['purchasing_price'];
        $product->selling_price = $request['selling_price'];

        if ($request['quantity'])
            $product->quantity = $request['quantity'];

        if ($request->hasFile('image')) {

            // Get filename with extension
            $filenameWithExt = $request->file('image')->getClientOriginalName();

            // Get just the filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get extension
            $extension = $request->file('image')->getClientOriginalExtension();

            // Create new filename
            $filenameToStore = $filename.'_'.time().'.'.$extension;

            // Uplaod image
            $path = $request->file('image')->storeAs('public/product_images/', $filenameToStore);

            $product->image = $filenameToStore;

        }

        $product->save();

        return response()->json(['data' => $product], 200);
    }

    public function getProducts($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Products, 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);

        if(!$product){
            return response()->json(['errors' => 'There is no product with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'purchasing_price' => 'numeric',
                'selling_price' => 'numeric',
                'image' => ['image','mimes:jpeg,jpg,png'],
                'quantity' => 'integer',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $product->name = $request['name'];
        if($request['purchasing_price'])
            $product->purchasing_price = $request['purchasing_price'];
        if($request['selling_price'])
            $product->selling_price = $request['selling_price'];
        if($request['quantity'])
            $product->quantity = $request['quantity'];
        if ($request->hasFile('image')) {

            // Get filename with extension
            $filenameWithExt = $request->file('image')->getClientOriginalName();

            // Get just the filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get extension
            $extension = $request->file('image')->getClientOriginalExtension();

            // Create new filename
            $filenameToStore = $filename.'_'.time().'.'.$extension;

            // Uplaod image
            $path = $request->file('image')->storeAs('public/product_images/', $filenameToStore);

            $product->image = $filenameToStore;
    
        }
        
        $product->save();

        return response()->json(['data' => $product], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if(!$product){
            return response()->json(['errors' => 'There is no product with this id !'], 400);
        }

        $product->delete();
        return response()->json(['message' => "Product Deleted"], 200);
    }
}
