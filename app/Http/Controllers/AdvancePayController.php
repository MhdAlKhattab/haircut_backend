<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Advance_Pay;
use App\Models\Branch;

class AdvancePayController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'employee_id' => 'required|numeric|exists:employees,id',
            'amount' => 'required|numeric',
            'source' => 'required|string',
        ]);
    }

    public function addAdvancePay(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $advance_pay = new Advance_Pay;

        $advance_pay->branch_id = $request['branch_id'];
        $advance_pay->employee_id = $request['employee_id'];
        $advance_pay->amount = $request['amount'];
        $advance_pay->source = $request['source'];

        $advance_pay->save();

        return response()->json(['data' => $advance_pay], 200);
    }

    public function getAdvancePays($branch_id)
    {
        $advance_pays = Advance_Pay::where('branch_id', '=', $branch_id)
                        ->with('Employee:id,name')
                        ->get();

        return response()->json($advance_pays, 200);
    }

    public function filterAdvancePays(Request $request, $branch_id)
    {
        $validatedData = Validator::make($request->all(),
            [
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        $advance_pays = Advance_Pay::where('branch_id', '=', $branch_id)
                        ->whereBetween('created_at', [$request['start_date'], $request['end_date']])
                        ->with('Employee:id,name')
                        ->get();

        return response()->json($advance_pays, 200);
    }

    public function searchAdvancePays(Request $request, $branch_id)
    {
        $advance_pays = Advance_Pay::where('branch_id', '=', $branch_id)
                        ->with('Employee:id,name')
                        ->whereHas('Employee', function($q) use($request) {
                            $q->where('name', 'LIKE', '%' . $request['query'] . '%');
                        })
                        ->get();

        return response()->json($advance_pays, 200);
    }

    public function updateAdvancePay(Request $request, $id)
    {
        $advance_pay = Advance_Pay::find($id);

        if(!$advance_pay){
            return response()->json(['errors' => 'There is no advance pay with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'numeric|exists:employees,id',
                'amount' => 'numeric',
                'source' => 'string',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['employee_id'])
            $advance_pay->employee_id = $request['employee_id'];
        if($request['amount'])
            $advance_pay->amount = $request['amount'];
        if($request['source'])
            $advance_pay->source = $request['source'];
        
        $advance_pay->save();

        return response()->json(['data' => $advance_pay], 200);
    }

    public function deleteAdvancePay($id)
    {
        $advance_pay = Advance_Pay::find($id);

        if(!$advance_pay){
            return response()->json(['errors' => 'There is no advance pay with this id !'], 400);
        }

        $advance_pay->delete();
        
        return response()->json(['message' => "Advance Pay Deleted"], 200);
    }
}
