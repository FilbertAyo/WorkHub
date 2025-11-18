<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Petty;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    View::composer('*', function ($view) {
        $pendingCount = 0;
        $approvedCount = 0;
        $processingCount = 0;

        if (Auth::check()) {
            $departmentId = Auth::user()->department_id;

            $pendingCount = Petty::where('department_id', $departmentId)
                                 ->where('status', 'pending')
                                 ->count();

            $approvedCount = Petty::where('department_id', $departmentId)
                                  ->where('status', 'approved')
                                  ->count();
        }

        // This one doesn't depend on user
        $processingCount = Petty::where('status', 'processing')->count();

        $view->with([
            'pendingPettiesCount'    => $pendingCount,
            'processingPettiesCount' => $processingCount,
            'approvedPettiesCount'   => $approvedCount,
        ]);
    });
}

}
