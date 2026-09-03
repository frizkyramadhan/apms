<?php

namespace App\Http\Controllers\Dmbd;

use App\Http\Controllers\Controller;
use App\Models\FleetUnit;
use App\Models\OperationalEvent;
use App\Support\BdAges;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $site = $request->string('site')->toString();
        $status = $request->string('status')->toString();

        $units = FleetUnit::query()
            ->with('openEvent')
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereIn('project_code', $user->projectCodes() ?: ['']))
            ->when($site !== '', fn ($q) => $q->where('project_code', $site))
            ->orderBy('unit_no')
            ->get()
            ->map(function (FleetUnit $unit) {
                $op = $unit->operationalStatus();
                $open = $unit->openEvent;

                return [
                    'unit' => $unit,
                    'status' => $op,
                    'priority' => $open?->priority,
                    'started_at' => $open?->started_at,
                    'bd_ages' => $open?->status === 'breakdown' ? BdAges::hours($open->started_at) : null,
                    'bd_ages_tip' => $open?->status === 'breakdown' ? BdAges::tooltip($open->started_at) : null,
                    'hm' => $open?->hm_corrected ?? $open?->hm_snapshot,
                    'mr_pr_po' => trim(implode(' / ', array_filter([$open?->mr_no, $open?->pr_no, $open?->po_no]))),
                    'problem' => $open?->problem,
                ];
            })
            ->when($status !== '', fn ($rows) => $rows->where('status', $status)->values());

        $sites = FleetUnit::query()
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereIn('project_code', $user->projectCodes() ?: ['']))
            ->distinct()
            ->orderBy('project_code')
            ->pluck('project_code');

        $counts = [
            'total' => $units->count(),
            'ready' => $units->where('status', 'ready')->count(),
            'breakdown' => $units->where('status', 'breakdown')->count(),
            'standby' => $units->where('status', 'standby')->count(),
        ];

        $month = $request->date('month')?->startOfMonth() ?? now()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $closedBd = OperationalEvent::query()
            ->where('status', 'breakdown')
            ->whereNotNull('ended_at')
            ->whereBetween('ended_at', [$month, $monthEnd])
            ->when($site !== '', fn ($q) => $q->whereHas('unit', fn ($u) => $u->where('project_code', $site)))
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereHas('unit', fn ($u) => $u->whereIn('project_code', $user->projectCodes() ?: [''])))
            ->get();

        $mttr = $closedBd->count() === 0
            ? null
            : round($closedBd->sum(fn (OperationalEvent $e) => $e->started_at->diffInHours($e->ended_at)) / $closedBd->count(), 1);

        $failures = OperationalEvent::query()
            ->where('status', 'breakdown')
            ->whereBetween('started_at', [$month, $monthEnd])
            ->when($site !== '', fn ($q) => $q->whereHas('unit', fn ($u) => $u->where('project_code', $site)))
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereHas('unit', fn ($u) => $u->whereIn('project_code', $user->projectCodes() ?: [''])))
            ->count();

        return view('content.dmbd.dashboard', [
            'rows' => $units,
            'sites' => $sites,
            'site' => $site,
            'status' => $status,
            'counts' => $counts,
            'mttr' => $mttr,
            'mtbf' => $failures === 0 ? null : null,
            'month' => $month,
            'failures' => $failures,
        ]);
    }
}
