<?php

namespace App\Http\Controllers\Dmbd;

use App\Http\Controllers\Controller;
use App\Models\FleetUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $units = FleetUnit::query()
            ->with('openEvent')
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereIn('project_code', $user->projectCodes() ?: ['']))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('unit_no', 'like', $term)->orWhere('model_name', 'like', $term));
            })
            ->orderBy('unit_no')
            ->paginate(50)
            ->withQueryString();

        return view('content.dmbd.units', ['units' => $units]);
    }
}
