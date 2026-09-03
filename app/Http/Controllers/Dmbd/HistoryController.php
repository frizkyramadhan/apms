<?php

namespace App\Http\Controllers\Dmbd;

use App\Http\Controllers\Controller;
use App\Models\OperationalEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $events = OperationalEvent::query()
            ->with('unit')
            ->whereNotNull('ended_at')
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereHas('unit', fn ($u) => $u->whereIn('project_code', $user->projectCodes() ?: [''])))
            ->when($request->filled('site'), fn ($q) => $q->whereHas('unit', fn ($u) => $u->where('project_code', $request->string('site'))))
            ->orderByDesc('started_at')
            ->limit(200)
            ->get();

        return view('content.dmbd.history', ['events' => $events]);
    }
}
