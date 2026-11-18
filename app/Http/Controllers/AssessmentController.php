<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Inventory;
use App\Models\ReliefRequest;
use App\Models\Zone;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $disasters = Disaster::orderBy('name')->get();
        $zones = Zone::when($request->disaster_id, function ($q) use ($request) {
            $q->where('disaster_id', $request->disaster_id);
        })->orderBy('area_name')->get();

        $resources = ReliefRequest::query()
            ->when($request->disaster_id, fn($q) => $q->where('disaster_id', $request->disaster_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->whereNotNull('resource_name')
            ->select('resource_name')->distinct()->orderBy('resource_name')->pluck('resource_name');

        $showAll = $request->input('scope') === 'all';

        // Base query for demand (active/unresolved by default)
        $demandQuery = ReliefRequest::query()
            ->selectRaw('disaster_id, zone_id, resource_name, SUM(quantity_requested) as total_demand')
            ->whereNotNull('resource_name');
        if (!$showAll) {
            $demandQuery->unresolved();
        }
        $demandQuery->when($request->disaster_id, fn($q) => $q->where('disaster_id', $request->disaster_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->resource_name, fn($q) => $q->where('resource_name', $request->resource_name))
            ->groupBy('disaster_id', 'zone_id', 'resource_name');

        $demandRows = $demandQuery->get();

        // Inventory rows keyed for lookup
        $inventoryQuery = Inventory::query()
            ->when($request->disaster_id, fn($q) => $q->where('disaster_id', $request->disaster_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->resource_name, fn($q) => $q->where('resource_name', $request->resource_name));
        $inventoryMap = [];
        foreach ($inventoryQuery->get() as $inv) {
            $key = $inv->disaster_id . '|' . $inv->zone_id . '|' . $inv->resource_name;
            $inventoryMap[$key] = $inv->quantity_in_stock;
        }

        $rows = [];
        foreach ($demandRows as $d) {
            $key = $d->disaster_id . '|' . $d->zone_id . '|' . $d->resource_name;
            $stock = $inventoryMap[$key] ?? 0;
            $demand = (int) $d->total_demand;
            $rows[] = [
                'disaster' => Disaster::find($d->disaster_id),
                'zone' => Zone::find($d->zone_id),
                'resource_name' => $d->resource_name,
                'total_demand' => $demand,
                'available_stock' => (int) $stock,
                'shortage' => max($demand - $stock, 0),
                'surplus' => max($stock - $demand, 0),
            ];
            unset($inventoryMap[$key]);
        }

        // Include inventory-only rows (stock exists, no matching demand in current scope)
        foreach ($inventoryMap as $key => $stock) {
            [$disasterId, $zoneId, $resourceName] = explode('|', $key, 3);
            $rows[] = [
                'disaster' => Disaster::find($disasterId),
                'zone' => Zone::find($zoneId),
                'resource_name' => $resourceName,
                'total_demand' => 0,
                'available_stock' => (int) $stock,
                'shortage' => 0,
                'surplus' => (int) $stock,
            ];
        }

        usort($rows, function ($a, $b) {
            return [$a['disaster']->name ?? '', $a['zone']->area_name ?? '', $a['resource_name']]
                <=> [$b['disaster']->name ?? '', $b['zone']->area_name ?? '', $b['resource_name']];
        });

        return view('admin.assessment_grid', compact('rows', 'disasters', 'zones', 'resources', 'showAll'));
    }
}
