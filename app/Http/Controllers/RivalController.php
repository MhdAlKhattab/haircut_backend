<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Rival;
use App\Models\Branch;

class RivalController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'employee_id' => 'required|numeric|exists:employees,id',
            'amount' => 'required|numeric',
            'reason' => 'required|string',
        ]);
    }

    public function addRival(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $rival = new Rival;

        $rival->branch_id = $request['branch_id'];
        $rival->employee_id = $request['employee_id'];
        $rival->amount = $request['amount'];
        $rival->reason = $request['reason'];

        $rival->save();

        return response()->json(['data' => $rival], 200);
    }

    public function getRivals($branch_id)
    {
        $rivals = Rival::where('branch_id', '=', $branch_id)
                        ->with('Employee:id,name')
                        ->get();

        return response()->json($rivals, 200);
    }

    public function searchRivals(Request $request, $branch_id)
    {
        $rivals = Rival::where('branch_id', '=', $branch_id)
                        ->with('Employee:id,name')
                        ->whereHas('Employee', function($q) use($request) {
                            $q->where('name', 'LIKE', '%' . $request['query'] . '%');
                        })
                        ->get();

        return response()->json($rivals, 200);
    }

    public function updateRival(Request $request, $id)
    {
        $rival = Rival::find($id);

        if(!$rival){
            return response()->json(['errors' => 'There is no rival with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'numeric|exists:employees,id',
                'amount' => 'numeric',
                'reason' => 'string',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['employee_id'])
            $rival->employee_id = $request['employee_id'];
        if($request['amount'])
            $rival->amount = $request['amount'];
        if($request['reason'])
            $rival->reason = $request['reason'];
        
        $rival->save();

        return response()->json(['data' => $rival], 200);
    }

    public function deleteRival($id)
    {
        $rival = Rival::find($id);

        if(!$rival){
            return response()->json(['errors' => 'There is no rival with this id !'], 400);
        }

        $rival->delete();
        
        return response()->json(['message' => "Rival Deleted"], 200);
    }
}
