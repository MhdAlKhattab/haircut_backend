<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cashier_Withdraw;
use App\Models\Branch;

class CashierWithdrawController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'amount' => 'required|numeric',
            'statement' => 'required|string',
        ]);
    }

    public function addCashierWithdraw(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $branch = Branch::find($request['branch_id'])->first();

        if ($branch->balance < $request['amount'])
            return response()->json(['errors' => "There is no enough money in the cashier"], 400);

        $withdraw = new Cashier_Withdraw;

        $withdraw->branch_id = $request['branch_id'];
        $withdraw->amount = $request['amount'];
        $withdraw->statement = $request['statement'];
        $withdraw->opening_balance = $branch->balance;
        $withdraw->closing_balance = $branch->balance - $request['amount'];
        $withdraw->save();

        $branch->balance -= $request['amount'];
        $branch->save();

        return response()->json(['data' => $withdraw], 200);
    }

    public function getCashierWithdraws($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Withdraws, 200);
    }

    public function filterCashierWithdraws(Request $request, $branch_id)
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

        $withdraws = Cashier_Withdraw::where('branch_id', '=', $branch_id)
        ->whereBetween('created_at', [$request['start_date'], $request['end_date']])
        ->get();

        return response()->json($withdraws, 200);
    }

    public function updateCashierWithdraw(Request $request, $id)
    {
        $withdraw = Cashier_Withdraw::find($id);

        if(!$withdraw){
            return response()->json(['errors' => 'There is no withdraw with this id !'], 400);
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
            if ($withdraw->opening_balance < $request['amount'])
                return response()->json(['errors' => "There was no enough money in the cashier"], 400);
            
            $branch = Branch::find($withdraw->branch_id)->first();
            $branch->balance += $withdraw->amount;
            $branch->balance -= $request['amount'];
            $branch->save();

            $withdraw->amount = $request['amount'];
            $withdraw->closing_balance = $withdraw->opening_balance - $request['amount'];
        }
        if($request['statement'])
            $withdraw->statement = $request['statement'];
        
        $withdraw->save();

        return response()->json(['data' => $withdraw], 200);
    }

    public function deleteCashierWithdraw($id)
    {
        $withdraw = Cashier_Withdraw::find($id);

        if(!$withdraw){
            return response()->json(['errors' => 'There is no withdraw with this id !'], 400);
        }

        $withdraw->delete();
        
        return response()->json(['message' => "Withdraw Deleted"], 200);
    }
}
