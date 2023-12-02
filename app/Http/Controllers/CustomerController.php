<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Branch;

class CustomerController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|numeric|unique:customers',     
        ]);
    }

    public function addCustomer(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $customer = new Customer;
        
        $customer->branch_id = $request['branch_id'];
        $customer->name = $request['name'];
        $customer->phone_number = $request['phone_number'];

        $customer->save();

        return response()->json(['data' => $customer], 200);
    }

    public function getCustomers($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Customers, 200);
    }

    public function searchCustomers(Request $request, $branch_id)
    {
        $customers = Customer::where([
            ['branch_id', '=', $branch_id],
            ['name', 'LIKE', '%' . $request['query'] . '%']
        ])->get();

        return response()->json($customers, 200);
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::find($id);

        if(!$customer){
            return response()->json(['errors' => 'There is no customer with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'phone_number' => 'numeric|unique:users', 
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $customer->name = $request['name'];
        if($request['phone_number'])
            $customer->phone_number = $request['phone_number'];

        $customer->save();

        return response()->json(['data' => $customer], 200);
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::find($id);

        if(!$customer){
            return response()->json(['errors' => 'There is no customer with this id !'], 400);
        }

        $customer->delete();
        return response()->json(['message' => "Customer Deleted"], 200);
    }
}
