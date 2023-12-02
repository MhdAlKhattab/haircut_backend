<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\General_Service_Provider;
use App\Models\Branch;

class GeneralServiceProviderController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'tax_state' => 'required|boolean',
            'tax_number' => 'numeric|unique:general__service__providers',     
        ]);
    }

    public function addProvider(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails()){
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        if ($request['tax_state'] and !$request['tax_number']){
            return response()->json(['errors' => 'You must send tax number!'], 400);
        }

        $provider = new General_Service_Provider;

        $provider->branch_id = $request['branch_id'];
        $provider->name = $request['name'];
        $provider->tax_state = $request['tax_state'];
        if ($request['tax_state'])
            $provider->tax_number = $request['tax_number'];
        else
            $provider->tax_number = -1;

        $provider->save();

        return response()->json(['data' => $provider], 200);
    }

    public function getProviders($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->General_Service_Providers, 200);
    }

    public function getUntaxedProviders($branch_id)
    {
        $providers = General_Service_Provider::where([
            ['branch_id', '=', $branch_id],
            ['tax_state', '=', 0]
        ])->get();

        return response()->json($providers, 200);
    }

    public function gettaxedProviders($branch_id)
    {
        $providers = General_Service_Provider::where([
            ['branch_id', '=', $branch_id],
            ['tax_state', '=', 1]
        ])->get();

        return response()->json($providers, 200);
    }

    public function updateProvider(Request $request, $id)
    {
        $provider = General_Service_Provider::find($id);

        if(!$provider){
            return response()->json(['errors' => 'There is no provider with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'tax_state' => 'boolean',
                'tax_number' => 'numeric|unique:general__service__providers', 
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $provider->name = $request['name'];
        if($request['tax_state'] != null)
            $provider->tax_state = $request['tax_state'];
        if($request['tax_number'])
            $provider->tax_number = $request['tax_number'];

        $provider->save();

        return response()->json(['data' => $provider], 200);
    }

    public function deleteProvider($id)
    {
        $provider = General_Service_Provider::find($id);

        if(!$provider){
            return response()->json(['errors' => 'There is no provider with this id !'], 400);
        }

        $provider->delete();
        return response()->json(['message' => "Provider Deleted"], 200);
    }
}
