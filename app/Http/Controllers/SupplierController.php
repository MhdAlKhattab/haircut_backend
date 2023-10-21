<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Supplier;
use App\Models\Branch;

class SupplierController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'tax_number' => 'required|numeric|unique:suppliers',     
        ]);
    }

    public function addSupplier(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $supplier = new Supplier;

        $supplier->branch_id = $request['branch_id'];
        $supplier->name = $request['name'];
        $supplier->tax_number = $request['tax_number'];

        $supplier->save();

        return response()->json(['data' => $supplier], 200);
    }

    public function getSuppliers($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Suppliers, 200);
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if(!$supplier){
            return response()->json(['errors' => 'There is no supplier with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'tax_number' => 'numeric|unique:suppliers',   
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $supplier->name = $request['name'];
        if($request['tax_number'])
            $supplier->tax_number = $request['tax_number'];

        $supplier->save();

        return response()->json(['data' => $supplier], 200);
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);

        if(!$supplier){
            return response()->json(['errors' => 'There is no supplier with this id !'], 400);
        }

        $supplier->delete();
        return response()->json(['message' => "Supplier Deleted"], 200);
    }
}
