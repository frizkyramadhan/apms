<?php

namespace App\Http\Controllers\Dmbd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyMonitoringController extends Controller
{
    public function __invoke(Request $request): View
    {
        $request->merge(['status' => $request->string('status')->toString() ?: 'breakdown']);

        return app(DashboardController::class)($request)
            ->with('pageTitle', 'Daily Monitoring');
    }
}
