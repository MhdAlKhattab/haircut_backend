<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use App\Models\Branch;

class ServiceController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'duration' => 'required|integer',
        ]);
    }

    public function addService(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $service = new Service;

        $service->branch_id = $request['branch_id'];
        $service->name = $request['name'];
        $service->price = $request['price'];
        $service->image = $request['image'];
        $service->duration = $request['duration'];

        $service->save();

        return response()->json(['data' => $service], 200);
    }

    public function getServices($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Services, 200);
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::find($id);

        if(!$service){
            return response()->json(['errors' => 'There is no service with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'price' => 'numeric',
                'image' => 'string',
                'duration' => 'integer',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $service->name = $request['name'];
        if($request['price'])
            $service->price = $request['price'];
        if($request['image'])
            $service->image = $request['image'];
        if($request['duration'])
            $service->duration = $request['duration'];

        $service->save();

        return response()->json(['data' => $service], 200);
    }

    public function deleteService($id)
    {
        $service = Service::find($id);

        if(!$service){
            return response()->json(['errors' => 'There is no service with this id !'], 400);
        }

        $service->delete();
        return response()->json(['message' => "Service Deleted"], 200);
    }
}
