<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Petty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function redirect()
    {

        //This is very important but later should be moved to Console/Command for automatic update
        DB::table('petties')
            ->where('status', 'resubmission')
            ->where('updated_at', '<', now()->subDay())
            ->update(['status' => 'rejected']);

        Log::info('Dashboard-triggered auto-reject of stale petty cash requests.');

        //End of it

        $userNo = User::all()->count();
        $pettyNo = Petty::all()->count();
        $paidAmount = Petty::where('status', 'paid')->sum('amount');
        $myExpense = Petty::where('user_id', Auth::user()->id)->where('status', 'paid')->sum('amount');
        $branchNo = Branch::all()->count();
        $departmentNo = Department::all()->count();

        // Status breakdown for pie chart
        $statusBreakdown = [
            'pending' => Petty::where('status', 'pending')->count(),
            'approved' => Petty::where('status', 'approved')->count(),
            'paid' => Petty::where('status', 'paid')->count(),
            'rejected' => Petty::where('status', 'rejected')->count(),
            'resubmission' => Petty::where('status', 'resubmission')->count(),
        ];

        // Monthly expense trend (last 6 months)
        $monthlyExpenses = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->format('M Y');
            $monthlyExpenses[] = Petty::where('status', 'paid')
                ->whereYear('paid_date', $date->year)
                ->whereMonth('paid_date', $date->month)
                ->sum('amount');
        }

        // Top 5 departments by spending
        $topDepartments = Petty::where('status', 'paid')
            ->join('departments', 'petties.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('SUM(petties.amount) as total'))
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent requests (last 5)
        $recentRequests = Petty::with(['user', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Current month statistics
        $currentMonthTotal = Petty::where('status', 'paid')
            ->whereYear('paid_date', Carbon::now()->year)
            ->whereMonth('paid_date', Carbon::now()->month)
            ->sum('amount');

        $lastMonthTotal = Petty::where('status', 'paid')
            ->whereYear('paid_date', Carbon::now()->subMonth()->year)
            ->whereMonth('paid_date', Carbon::now()->subMonth()->month)
            ->sum('amount');

        $percentageChange = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // Active users (users who made requests this month)
        $activeUsers = Petty::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->distinct('user_id')
            ->count('user_id');

        // Individual user analytics
        $myTotalRequests = Petty::where('user_id', Auth::user()->id)->count();
        $myApprovedRequests = Petty::where('user_id', Auth::user()->id)->where('status', 'approved')->count();
        $myPendingRequests = Petty::where('user_id', Auth::user()->id)->where('status', 'pending')->count();

        // My status breakdown
        $myStatusBreakdown = [
            'pending' => Petty::where('user_id', Auth::user()->id)->where('status', 'pending')->count(),
            'approved' => Petty::where('user_id', Auth::user()->id)->where('status', 'approved')->count(),
            'paid' => Petty::where('user_id', Auth::user()->id)->where('status', 'paid')->count(),
            'rejected' => Petty::where('user_id', Auth::user()->id)->where('status', 'rejected')->count(),
            'resubmission' => Petty::where('user_id', Auth::user()->id)->where('status', 'resubmission')->count(),
        ];

        // My monthly request trend (last 6 months)
        $myMonthlyRequests = [];
        $myMonthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $myMonthLabels[] = $date->format('M Y');
            $myMonthlyRequests[] = Petty::where('user_id', Auth::user()->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // My recent requests (last 10)
        $myRecentRequests = Petty::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'userNo',
            'pettyNo',
            'paidAmount',
            'myExpense',
            'branchNo',
            'departmentNo',
            'statusBreakdown',
            'monthlyExpenses',
            'monthLabels',
            'topDepartments',
            'recentRequests',
            'currentMonthTotal',
            'percentageChange',
            'activeUsers',
            'myTotalRequests',
            'myApprovedRequests',
            'myPendingRequests',
            'myStatusBreakdown',
            'myMonthlyRequests',
            'myMonthLabels',
            'myRecentRequests'
        ));
    }


}
