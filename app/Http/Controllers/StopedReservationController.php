<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Stoped_Reservation;
use App\Models\Branch;

class StopedReservationController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'employee_id' => 'required|numeric|exists:employees,id',
            'date' => 'required|date_format:Y-m-d|after:today',
        ]);
    }

    public function addStopedReservation(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $stoped_reservations = Stoped_Reservation::where([
                                    ['branch_id', '=', $request['branch_id']],
                                    ['employee_id', '=', $request['employee_id']],
                                    ['date', '=', $request['date']],
                                ])->get();
        
        if (count($stoped_reservations) != 0){
            return response()->json(['errors' => 'This employee already have a dayoff in this date !'], 400);
        }

        $stoped_reservation = new Stoped_Reservation;

        $stoped_reservation->branch_id = $request['branch_id'];
        $stoped_reservation->employee_id = $request['employee_id'];
        $stoped_reservation->date = $request['date'];

        $stoped_reservation->save();

        return response()->json(['data' => $stoped_reservation], 200);
    }

    public function getStopedReservations($branch_id)
    {
        $stoped_reservations = Stoped_Reservation::where('branch_id', '=', $branch_id)
                        ->with('Employee:id,name')
                        ->get();

        return response()->json($stoped_reservations, 200);
    }

    public function searchStopedReservations(Request $request, $branch_id)
    {
        $stoped_reservations = Stoped_Reservation::where('branch_id', '=', $branch_id)
                                ->with('Employee:id,name')
                                ->whereHas('Employee', function($q) use($request) {
                                    $q->where('name', 'LIKE', '%' . $request['query'] . '%');
                                })
                                ->get();

        return response()->json($stoped_reservations, 200);
    }

    public function filterStopedReservations(Request $request, $branch_id)
    {
        $validatedData = Validator::make($request->all(),
        [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        $stoped_reservations = Stoped_Reservation::where('branch_id', '=', $branch_id)
                                ->whereBetween('date', [$request['start_date'], $request['end_date']])
                                ->with('Employee:id,name')
                                ->get();

        return response()->json($stoped_reservations, 200);
    }

    public function updateStopedReservation(Request $request, $id)
    {
        $stoped_reservation = Stoped_Reservation::find($id);

        if(!$stoped_reservation){
            return response()->json(['errors' => 'There is no stoped reservation with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'numeric|exists:employees,id',
                'date' => 'date_format:Y-m-d|after:today',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['employee_id'])
            $stoped_reservation->employee_id = $request['employee_id'];
        if($request['date'])
            $stoped_reservation->date = $request['date'];

        $stoped_reservations = Stoped_Reservation::where([
            ['branch_id', '=', $stoped_reservation->branch_id],
            ['employee_id', '=', $stoped_reservation->employee_id],
            ['date', '=', $stoped_reservation->date],
        ])->get();

        if (count($stoped_reservations) != 0){
            return response()->json(['errors' => 'This employee already have a dayoff in this date !'], 400);
        }

        $stoped_reservation->save();
        
        return response()->json(['data' => $stoped_reservation], 200);
    }

    public function deleteStopedReservation($id)
    {
        $stoped_reservation = Stoped_Reservation::find($id);

        if(!$stoped_reservation){
            return response()->json(['errors' => 'There is no stoped reservation with this id !'], 400);
        }

        $stoped_reservation->delete();

        return response()->json(['message' => "Stoped Reservation Deleted"], 200);
    }
}
