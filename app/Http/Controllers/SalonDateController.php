<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Salon_Date;
use App\Models\Branch;

class SalonDateController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'day' => 'required|string|unique:salon__dates',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
    }

    public function addSalonDate(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $date = new Salon_Date;

        $date->branch_id = $request['branch_id'];
        $date->day = $request['day'];
        $date->start_time = $request['start_time'];
        $date->end_time = $request['end_time'];

        $date->save();

        return response()->json(['data' => $date], 200);
    }

    public function getSalonDates($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Salon_Dates, 200);
    }

    public function updateSalonDate(Request $request, $id)
    {
        $date = Salon_Date::find($id);

        if(!$date){
            return response()->json(['errors' => 'There is no date with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'day' => 'string|unique:salon__dates',
                'start_time' => 'date_format:H:i',
                'end_time' => 'date_format:H:i|after:start_time',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['day'])
            $date->day = $request['day'];
        if($request['start_time'])
            $date->start_time = $request['start_time'];
        if($request['end_time'])
            $date->end_time = $request['end_time'];

        $date->save();

        return response()->json(['data' => $date], 200);
    }

    public function deleteSalonDate($id)
    {
        $date = Salon_Date::find($id);

        if(!$date){
            return response()->json(['errors' => 'There is no date with this id !'], 400);
        }

        $date->delete();
        return response()->json(['message' => "Salon Date Deleted"], 200);
    }
}
