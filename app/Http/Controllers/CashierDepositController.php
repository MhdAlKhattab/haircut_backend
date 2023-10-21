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

        $deposit = new Cashier_Deposit;

        $deposit->branch_id = $request['branch_id'];
        $deposit->amount = $request['amount'];
        $deposit->statement = $request['statement'];

        $deposit->save();

        return response()->json(['data' => $deposit], 200);
    }

    public function getCashierDeposits($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Deposits, 200);
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

        if($request['amount'])
            $deposit->amount = $request['amount'];
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
