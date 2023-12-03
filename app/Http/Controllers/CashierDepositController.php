<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cashier_Deposit;
use App\Models\Branch;

class CashierDepositController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'amount' => 'required|numeric',
            'statement' => 'required|string',
        ]);
    }

    public function addCashierDeposit(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $branch = Branch::find($request['branch_id'])->first();

        $deposit = new Cashier_Deposit;

        $deposit->branch_id = $request['branch_id'];
        $deposit->amount = $request['amount'];
        $deposit->statement = $request['statement'];
        $deposit->opening_balance = $branch->balance;
        $deposit->closing_balance = $branch->balance + $request['amount'];
        $deposit->save();

        $branch->balance += $request['amount'];
        $branch->save();

        return response()->json(['data' => $deposit], 200);
    }

    public function getCashierDeposits($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Deposits, 200);
    }

    public function filterCashierDeposits(Request $request, $branch_id)
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

        $deposits = Cashier_Deposit::where('branch_id', '=', $branch_id)
        ->whereBetween('created_at', [$request['start_date'], $request['end_date']])
        ->get();

        return response()->json($deposits, 200);
    }

    public function updateCashierDeposit(Request $request, $id)
    {
        $deposit = Cashier_Deposit::find($id);

        if(!$deposit){
            return response()->json(['errors' => 'There is no deposit with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'amount' => 'numeric',
                'statement' => 'string',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['amount']){
            $branch = Branch::find($deposit->branch_id)->first();
            $branch->balance -= $deposit->amount;
            $branch->balance += $request['amount'];
            $branch->save();

            $deposit->amount = $request['amount'];
            $deposit->closing_balance = $deposit->opening_balance + $request['amount'];
        }
        if($request['statement'])
            $deposit->statement = $request['statement'];
        
        $deposit->save();

        return response()->json(['data' => $deposit], 200);
    }

    public function deleteCashierDeposit($id)
    {
        $deposit = Cashier_Deposit::find($id);

        if(!$deposit){
            return response()->json(['errors' => 'There is no deposit with this id !'], 400);
        }

        $deposit->delete();
        
        return response()->json(['message' => "Deposit Deleted"], 200);
    }
}
