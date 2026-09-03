<?php

namespace App\Http\Controllers\Dmbd;

use App\Http\Controllers\Controller;
use App\Models\FleetUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    private const SORT_COLUMNS = [
        'unit_no',
        'description',
        'project_code',
        'model_name',
        'manufacture',
        'plant_group',
        'plant_type',
        'unit_status',
    ];

    public function index(): View
    {
        return view('content.dmbd.units');
    }

    public function data(Request $request): JsonResponse
    {
        $user = $request->user();
        $scoped = FleetUnit::query()
            ->when(! $user->seesAllSites(), fn ($q) => $q->whereIn('project_code', $user->projectCodes() ?: ['']));

        $recordsTotal = (clone $scoped)->count();

        $filtered = (clone $scoped)
            ->when($request->filled('unitNo'), fn ($q) => $q->where('unit_no', 'like', '%'.$request->string('unitNo').'%'))
            ->when($request->filled('model'), fn ($q) => $q->where('model_name', 'like', '%'.$request->string('model').'%'))
            ->when($request->filled('project'), fn ($q) => $q->where('project_code', 'like', '%'.$request->string('project').'%'))
            ->when($request->filled('manufacture'), fn ($q) => $q->where('manufacture', 'like', '%'.$request->string('manufacture').'%'))
            ->when($request->filled('plantGroup'), fn ($q) => $q->where('plant_group', 'like', '%'.$request->string('plantGroup').'%'))
            ->when($request->filled('status'), fn ($q) => $q->matchingFleetStatus($request->string('status')->toString()));

        $recordsFiltered = (clone $filtered)->count();

        $sortIndex = (int) $request->input('order.0.column', 0);
        $sortCol = self::SORT_COLUMNS[$sortIndex] ?? 'unit_no';
        $sortDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $start = max(0, (int) $request->input('start', 0));
        $length = min(100, max(1, (int) $request->input('length', 10)));

        $rows = $filtered->orderBy($sortCol, $sortDir)->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows->map(fn (FleetUnit $unit) => [
                'unit_no' => $unit->unit_no,
                'description' => $unit->description,
                'project_code' => $unit->project_code,
                'model_name' => $unit->model_name,
                'manufacture' => $unit->manufacture,
                'plant_group' => $unit->plant_group,
                'plant_type' => $unit->plant_type,
                'unit_status' => $unit->unit_status,
            ]),
        ]);
    }
}
