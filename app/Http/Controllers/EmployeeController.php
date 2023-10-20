<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\Employee_Info;
use App\Models\Branch;

class EmployeeController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'residence_number' => 'required|numeric|unique:employees',     
            'residence_expire_date' => 'required|date_format:Y-m-d',     
            'health_number' => 'required|numeric|unique:employees',     
            'health_expire_date' => 'required|date_format:Y-m-d',     
            'job' => 'required|string|max:255',
            'pay_type' => 'required|string|in:salary,commission,both',
            'salary' => 'required|numeric',
            'income_limit' => 'numeric',
            'commission' => 'numeric|min:1|max:100',
            'residence_cost' => 'required|numeric',
            'health_cost' => 'required|numeric',
            'insurance_cost' => 'required|numeric',
            'costs_responsible' => 'required|string|in:salon,self',
        ]);
    }

    public function addEmployee(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $employee = Employee::create([
            'branch_id' => $request['branch_id'],
            'name' => $request['name'],
            'residence_number' => $request['residence_number'], 
            'residence_expire_date' => $request['residence_expire_date'],
            'health_number' => $request['health_number'],
            'health_expire_date' => $request['health_expire_date'], 
            'job' => $request['job'],
            'pay_type' => $request['pay_type'],
            'salary' => $request['salary'], 
            'income_limit' => $request['income_limit'],
            'commission' => $request['commission'],
            'residence_cost' => $request['residence_cost'], 
            'health_cost' => $request['health_cost'],
            'insurance_cost' => $request['insurance_cost'],
            'costs_responsible' => $request['costs_responsible'], 
        ]);
        $employee->save();

        $employee_info = Employee_Info::create([
            'employee_id' => $employee->id
        ]);
        $employee_info->save();

        return response()->json(['data' => $employee], 200);

        // Employee::with('Info')->where('id', '=', $employee->id)->first()
    }

    public function getEmployees($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Employees, 200);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            return response()->json(['errors' => 'There is no employee with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'residence_number' => 'numeric|unique:employees',     
                'residence_expire_date' => 'date_format:Y-m-d',     
                'health_number' => 'numeric|unique:employees',     
                'health_expire_date' => 'date_format:Y-m-d',     
                'job' => 'string|max:255',
                'pay_type' => 'string|in:salary,commission,both',
                'salary' => 'numeric',
                'income_limit' => 'numeric',
                'commission' => 'numeric|min:1|max:100',
                'residence_cost' => 'numeric',
                'health_cost' => 'numeric',
                'insurance_cost' => 'numeric',
                'costs_responsible' => 'string|in:salon,self',
                'state' => 'boolean',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'] != null)
            $employee->name = $request['name'];
        if($request['residence_number'] != null)
            $employee->residence_number = $request['residence_number'];
        if($request['residence_expire_date'] != null)
            $employee->residence_expire_date = $request['residence_expire_date'];
        if($request['health_number'] != null)
            $employee->health_number = $request['health_number'];
        if($request['health_expire_date'] != null)
            $employee->health_expire_date = $request['health_expire_date'];
        if($request['job'] != null)
            $employee->job = $request['job'];
        if($request['pay_type'] != null)
            $employee->pay_type = $request['pay_type'];
        if($request['salary'] != null)
            $employee->salary = $request['salary'];
        if($request['income_limit'] != null)
            $employee->income_limit = $request['income_limit'];
        if($request['commission'] != null)
            $employee->commission = $request['commission'];
        if($request['residence_cost'] != null)
            $employee->residence_cost = $request['residence_cost'];
        if($request['health_cost'] != null)
            $employee->health_cost = $request['health_cost'];
        if($request['insurance_cost'] != null)
            $employee->insurance_cost = $request['insurance_cost'];
        if($request['costs_responsible'] != null)
            $employee->costs_responsible = $request['costs_responsible'];
        if($request['state'] != null)
            $employee->state = $request['state'];

        $employee->save();

        return response()->json(['data' => $employee], 200);
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            return response()->json(['errors' => 'There is no employee with this id !'], 400);
        }

        $employee->delete();
        return response()->json(['message' => "Employee Deleted"], 200);
    }
}
