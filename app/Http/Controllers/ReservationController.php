<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Reservation;
use App\Models\Branch;

class ReservationController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'employee_id' => 'required|numeric|exists:employees,id',
            'customer_id' => 'required|numeric|exists:customers,id',
            'services' => 'required|array|min:1',
            'services.*' => 'required|numeric|exists:services,id',
            'date' => 'required|date_format:Y-m-d H:i|after:today',
            'total_duration' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);
    }

    public function addReservation(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $reservation = new Reservation;

        $reservation->branch_id = $request['branch_id'];
        $reservation->employee_id = $request['employee_id'];
        $reservation->customer_id = $request['customer_id'];
        $reservation->date = $request['date'];
        $reservation->total_duration = $request['total_duration'];
        $reservation->total_amount = $request['total_amount'];

        $reservation->save();

        $reservation->Services()->attach($request['services']);

        return response()->json(['data' => $reservation,
                                'services' => $request['services']], 200);
    }

    public function getReservations($branch_id)
    {
        $reservations = Reservation::where('branch_id', '=', $branch_id)
                        ->with(['Employee:id,name',
                                'Customer:id,name,phone_number',
                                'services:id,name'])
                        ->select('id', 'branch_id', 'employee_id', 'customer_id',
                                 'date', 'total_duration', 'total_amount')
                        ->get();

        return response()->json($reservations, 200);
    }

    public function searchReservations(Request $request, $branch_id)
    {
        $reservations = Reservation::where('branch_id', '=', $branch_id)
                        ->with(['Employee:id,name',
                                'Customer:id,name,phone_number',
                                'services:id,name'])
                        ->whereHas('Employee', function($q) use($request) {
                            $q->where('name', 'LIKE', '%' . $request['query'] . '%');
                        })
                        ->orWhereHas('Customer', function($q) use($request) {
                            $q->where('name', 'LIKE', '%' . $request['query'] . '%');
                        })
                        ->select('id', 'branch_id', 'employee_id', 'customer_id',
                                 'date', 'total_duration', 'total_amount')
                        ->get();

        return response()->json($reservations, 200);
    }

    public function filterReservations(Request $request, $branch_id)
    {
        $validatedData = Validator::make($request->all(),
        [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        $reservations = Reservation::where('branch_id', '=', $branch_id)
                        ->whereBetween('date', [$request['start_date'], $request['end_date']])
                        ->with(['Employee:id,name',
                                'Customer:id,name,phone_number',
                                'services:id,name'])
                        ->select('id', 'branch_id', 'employee_id', 'customer_id',
                                 'date', 'total_duration', 'total_amount')
                        ->get();

        return response()->json($reservations, 200);
    }

    public function updateReservation(Request $request, $id)
    {
        $reservation = Reservation::find($id);

        if(!$reservation){
            return response()->json(['errors' => 'There is no reservation with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'numeric|exists:employees,id',
                'customer_id' => 'numeric|exists:customers,id',
                'services' => 'array|min:1',
                'services.*' => 'numeric|exists:services,id',
                'date' => 'date_format:Y-m-d H:i|after:today',
                'total_duration' => 'numeric',
                'total_amount' => 'numeric',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['employee_id'])
            $reservation->employee_id = $request['employee_id'];
        if($request['customer_id'])
            $reservation->customer_id = $request['customer_id'];
        if($request['date'])
            $reservation->date = $request['date'];
        if($request['total_duration'])
            $reservation->total_duration = $request['total_duration'];
        if($request['total_amount'])
            $reservation->total_amount = $request['total_amount'];

        $reservation->save();

        if ($request['services'])
            $reservation->Services()->sync($request['services']);
        
        return response()->json(['data' => $reservation,
                                'services' => $request['services']], 200);
    }

    public function deleteReservation($id)
    {
        $reservation = Reservation::find($id);

        if(!$reservation){
            return response()->json(['errors' => 'There is no reservation with this id !'], 400);
        }

        $reservation->delete();
        return response()->json(['message' => "Reservation Deleted"], 200);
    }
}
