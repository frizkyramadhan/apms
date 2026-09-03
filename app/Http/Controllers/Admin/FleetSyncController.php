<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\FleetCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FleetSyncController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dmbd.master');
    }

    public function __invoke(Request $request, FleetCache $cache): JsonResponse|RedirectResponse
    {
        try {
            $result = $cache->sync();
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        $message = ($result['skipped'] ?? false)
            ? "ARK Fleet disabled. Derived {$result['models_synced']} models from cache."
            : "Synced {$result['synced']} units, {$result['models_synced']} models from ARK Fleet.";

        return back()->with('success', $message);
    }
}
