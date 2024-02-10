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
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{
    public function index($branch_id)
    {
        return response()->json([
            'statistics' => $this->getStatistics($branch_id),
            'global_report' => $this->globalReport($branch_id),
            'employee_revenues' => $this->employeeRevenues($branch_id),
        ], 200);
    }

    public function getStatistics($branch_id)
    {
        $daily_orders = DB::table('orders as o')
                        ->where('branch_id', '=', $branch_id)
                        ->select(array(DB::Raw('count(o.id) as Total_Orders'),
                                    DB::Raw('sum(o.amount_after_discount) as Total_Revenues'),
                                    DB::Raw('DATE(o.created_at) as day')))
                        ->groupBy('day')
                        ->get();

        // Compute avg daily orders and revenues
        $sumDailyOrders = 0;
        $sumDailyRevenues = 0;
        foreach($daily_orders as $order){
            $sumDailyOrders += $order->Total_Orders;
            $sumDailyRevenues += $order->Total_Revenues;
        }
        $avgDailyOrders = $sumDailyOrders / count($daily_orders);
        $avgDailyRevenues = $sumDailyRevenues / count($daily_orders);

        $weekly_orders = DB::table('orders as o')
                        ->where('branch_id', '=', $branch_id)
                        ->select(array(DB::Raw('count(o.id) as Total_Orders'),
                                    DB::Raw('sum(o.amount_after_discount) as Total_Revenues'),
                                    DB::Raw('WEEK(o.created_at) as week')))
                        ->groupBy('week')
                        ->get();

        // Compute avg Weekly orders and revenues
        $sumWeeklyOrders = 0;
        $sumWeeklyRevenues = 0;
        foreach($weekly_orders as $order){
            $sumWeeklyOrders += $order->Total_Orders;
            $sumWeeklyRevenues += $order->Total_Revenues;
        }
        $avgWeeklyOrders = $sumWeeklyOrders / count($weekly_orders);
        $avgWeeklyRevenues = $sumWeeklyRevenues / count($weekly_orders);

        return [
                'avg_daily_orders' => round($avgDailyOrders),
                'avg_daily_revenues' => round($avgDailyRevenues),
                'avg_weekly_orders'  => round($avgWeeklyOrders),
                'avg_weekly_revenues' => round($avgWeeklyRevenues),
            ];
    }

    public function globalReport($branch_id)
    {
        $date = Carbon::now()->toDateString();

        $totalOrders = Order::where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
                        ->count('id');

        $totalRevenues = Order::where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
                        ->sum('amount_after_discount');

        $onlineTotalRevenues = Order::where([
                                    ['branch_id', '=', $branch_id],
                                    ['amount_pay_type', 'LIKE', 'online'],
                                ])
                        ->whereDate('created_at', '=', $date)
                        ->sum('amount_after_discount');

        $cashTotalRevenues = Order::where([
                                    ['branch_id', '=', $branch_id],
                                    ['amount_pay_type', 'LIKE', 'cash'],
                                ])
                        ->whereDate('created_at', '=', $date)
                        ->sum('amount_after_discount');

        $totalDayCommissions = Order::where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
                        ->sum('employee_commission');

        $totalPurchases = Purchase::where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
                        ->sum('amount_after_discount');

        $sundryTotalPurchases = Purchase::where([
                                    ['branch_id', '=', $branch_id],
                                    ['type', 'LIKE', 'sundry'],
                                ])
                        ->whereDate('created_at', '=', $date)
                        ->sum('amount_after_discount');

        $generalServices = General_Service::where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
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

        return [
            'total_revenues' => round($totalRevenues),
            'online_total_revenues' => round($onlineTotalRevenues),
            'cash_total_revenues' => round($cashTotalRevenues),
            'total_orders' => round($totalOrders),
            'total_purchases' => round($totalPurchases),
            'sundry_total_purchases' => round($sundryTotalPurchases),
            'general_services' => round($generalServices),
            'total_commissions' => round($totalDayCommissions),
            'payed_commissions' => round($payedDayCommission),
            'remaining_commissions' => round($remainingDayCommission),
        ];
    }

    public function employeeRevenues($branch_id)
    {
        $date = Carbon::now()->toDateString();

        $employeeRevenues = DB::table('orders as o')
                        ->where('branch_id', '=', $branch_id)
                        ->whereDate('created_at', '=', $date)
                        ->select(array(
                                    DB::Raw('count(o.id) as Total_Orders'),
                                    DB::Raw('sum(o.amount_after_discount) as Total_Revenues'),
                                    DB::Raw('sum(o.employee_commission) as Total_Commissions'),
                                    DB::Raw('o.employee_id as employee_id'),
                                ))
                        ->groupBy('employee_id')
                        ->get();

        $employeesArray = [];
        $max_revenue = -1;
        $max_index = -1;
        foreach ($employeeRevenues as $object){
            // Check the top revenue of employees
            if ($object->Total_Revenues > $max_revenue){
                $max_revenue = $object->Total_Revenues;
                $max_index = count($employeesArray);
            }

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
                                        'total_revenues' => round($object->Total_Revenues),
                                        'total_commissions' => round($object->Total_Commissions),
                                        'payed_commissions' => round($payedCommission),
                                        'remaining_commissions' => round($remainingCommission),
                                    ];
        }

        return ['employees' => $employeesArray, 'top_employee_index' => $max_index];
    }
}
