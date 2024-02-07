<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee_Info;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\General_Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index($branch_id)
    {
        $day = Carbon::now()->day;

        $totalOrders = Order::where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->count('id');

        $totalRevenues = Order::where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount_after_discount');

        $onlineTotalRevenues = Order::where([
                                    ['branch_id', '=', $branch_id],
                                    ['amount_pay_type', 'LIKE', 'online'],
                                ])
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount_after_discount');

        $cashTotalRevenues = Order::where([
                                    ['branch_id', '=', $branch_id],
                                    ['amount_pay_type', 'LIKE', 'cash'],
                                ])
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount_after_discount');

        $totalDayCommissions = Order::where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->sum('employee_commission');

        $totalPurchases = Purchase::where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount_after_discount');

        $sundryTotalPurchases = Purchase::where([
                                    ['branch_id', '=', $branch_id],
                                    ['type', 'LIKE', 'sundry'],
                                ])
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount_after_discount');

        $generalServices = General_Service::where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->sum('amount');

        $employees = Employee::where('branch_id', '=', $branch_id)
                        ->select('id', 'name')
                        ->with('Info')
                        ->get();

        // Compute total commission and total payed commission for all employees 
        $totalCommmission = 0;
        $payedCommmission = 0;
        foreach ($employees as $employee){
            $totalCommmission += $employee->info->total_commission;
            $payedCommmission += $employee->info->payed_commission;
        }

        // Compute the difference between total commission and day's commission
        $differenceCommission = $totalCommmission - $totalDayCommissions;

        // Compute day's payed commission
        $payedDayCommission = 0;
        if ($payedCommmission > $differenceCommission) {
            $payedDayCommission = $payedCommmission - $differenceCommission;
        }

        // Compute day's remaining commission
        $remainingDayCommission = $totalDayCommissions - $payedDayCommission;

        return response()->json(['total_revenues' => $totalRevenues,
                        'online_total_revenues' => $onlineTotalRevenues,
                        'cash_total_revenues' => $cashTotalRevenues,
                        'total_orders' => $totalOrders,
                        'total_purchases' => $totalPurchases,
                        'sundry_total_purchases' => $sundryTotalPurchases,
                        'general_services' => $generalServices,
                        'total_commissions' => $totalDayCommissions,
                        'payed_commissions' => $payedDayCommission,
                        'remaining_commissions' => $remainingDayCommission,
                        ], 200);
    }

    public function employeeRevenues($branch_id)
    {
        $day = Carbon::now()->day;

        $employeeRevenues = DB::table('orders as o')
                        ->where('branch_id', '=', $branch_id)
                        ->whereDay('created_at', '=', $day)
                        ->select(array(
                                    DB::Raw('count(o.id) as Total_Orders'),
                                    DB::Raw('sum(o.amount_after_discount) as Total_Revenues'),
                                    DB::Raw('sum(o.employee_commission) as Total_Commissions'),
                                    DB::Raw('o.employee_id as employee_id'),
                                ))
                        ->groupBy('employee_id')
                        ->get();

        $employeesArray = [];
        foreach ($employeeRevenues as $object){
            $employee = Employee::with('Info')->find($object->employee_id);

            // Compute the difference between total commission and day's commission
            $differenceCommission = $employee->info->total_commission - $object->Total_Commissions;

            // Compute day's payed commission
            $payedCommission = 0;
            if ($employee->info->payed_commission > $differenceCommission) {
                $payedCommission = $employee->info->payed_commission - $differenceCommission;
            }

            // Compute day's remaining commission
            $remainingCommission = $object->Total_Commissions - $payedCommission;

            $employeesArray[] = (object) [
                                        'id' => $object->employee_id,
                                        'name' => $employee->name,
                                        'total_revenues' => $object->Total_Revenues,
                                        'total_commissions' => $object->Total_Commissions,
                                        'payed_commissions' => $payedCommission,
                                        'remaining_commissions' => $remainingCommission,
                                    ];
        }

        return $employeesArray;
    }
}
