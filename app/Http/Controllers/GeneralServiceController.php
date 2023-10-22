<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\General_Service;
use App\Models\Branch;

class GeneralServiceController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'provider_id' => 'required|numeric|exists:general__service__providers,id',
            'term_id' => 'required|numeric|exists:general__service__terms,id',
            'amount' => 'required|numeric',
            'tax_state' => 'required|boolean',
        ]);
    }

    public function addService(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $service = new General_Service;

        $service->branch_id = $request['branch_id'];
        $service->provider_id = $request['provider_id'];
        $service->term_id = $request['term_id'];
        $service->amount = $request['amount'];
        $service->tax_state = $request['tax_state'];

        $service->save();

        return response()->json(['data' => $service], 200);
    }

    public function getServices($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->General_Services, 200);
    }

    public function updateService(Request $request, $id)
    {
        $service = General_Service::find($id);

        if(!$service){
            return response()->json(['errors' => 'There is no service with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'provider_id' => 'numeric|exists:general__service__providers,id',
                'term_id' => 'numeric|exists:general__service__terms,id',
                'amount' => 'numeric',
                'tax_state' => 'boolean',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['provider_id'])
            $service->provider_id = $request['provider_id'];
        if($request['term_id'])
            $service->term_id = $request['term_id'];
        if($request['amount'])
            $service->amount = $request['amount'];
        if($request['tax_state'])
            $service->tax_state = $request['tax_state'];

        $service->save();

        return response()->json(['data' => $service], 200);
    }

    public function deleteService($id)
    {
        $service = General_Service::find($id);

        if(!$service){
            return response()->json(['errors' => 'There is no service with this id !'], 400);
        }

        $service->delete();
        return response()->json(['message' => "Service Deleted"], 200);
    }
}
