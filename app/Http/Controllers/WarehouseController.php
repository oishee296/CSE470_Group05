<?php

namespace App\Http\Controllers;

use App\Models\WarehouseHub;
use App\Models\Inventory;
use App\Models\IncomingAssetLog;
use App\Models\StorageLocker;
use App\Models\LockerAssignment;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = WarehouseHub::with([
        'inventories',
        'lockers.assignments.inventory'
        ])->get();

        $incomingAssets = IncomingAssetLog::with('inventory.warehouse')
          ->orderBy('received_at', 'desc')
          ->get();
        $totalWarehouses = WarehouseHub::count();
        $totalInventoryItems = Inventory::count();
        $totalIncomingAssets = IncomingAssetLog::count();
        $totalLockers = StorageLocker::count();
        $lockerAssignments = LockerAssignment::with([
          'locker.warehouse',
         'inventory'
          ])->get();

        return view('warehouse.inventory', compact(
            'warehouses',
            'incomingAssets',
            'totalWarehouses',
            'totalInventoryItems',
            'totalIncomingAssets',
            'totalLockers',
            'lockerAssignments'
        ));
    }
    public function create()
{
    return view('warehouse.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'total_capacity' => 'required|numeric|min:0.01'
    ]);

    WarehouseHub::create([
        'name' => $request->name,
        'location' => $request->location,
        'total_capacity' => $request->total_capacity
    ]);

    return redirect()->route('warehouse.index')
        ->with('success', 'Warehouse added successfully!');
}

public function createInventory()
{
    $warehouses = WarehouseHub::all();

    return view('warehouse.inventory-create', compact('warehouses'));
}

public function storeInventory(Request $request)
{
    $request->validate([
        'warehouse_id' => 'required|exists:warehouse_hubs,id',
        'item_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'unit' => 'required|string|max:255',
        'volume_per_unit' => 'required|numeric|min:0.001'
    ]);

    $warehouse = WarehouseHub::findOrFail($request->warehouse_id);

$usedCapacity = $warehouse->inventories()
    ->get()
    ->sum(function ($item) {
        return $item->quantity * $item->volume_per_unit;
    });

$newItemVolume = $request->quantity * $request->volume_per_unit;

if (($usedCapacity + $newItemVolume) > $warehouse->total_capacity) {
    return back()->withErrors([
        'quantity' => 'This warehouse does not have enough remaining capacity.'
    ])->withInput();
}

    Inventory::create([
        'warehouse_id' => $request->warehouse_id,
        'item_name' => $request->item_name,
        'quantity' => $request->quantity,
        'unit' => $request->unit,
        'volume_per_unit' => $request->volume_per_unit
    ]);

    return redirect()->route('warehouse.index')
        ->with('success', 'Inventory item added successfully!');
}

public function createIncomingAsset()
{
    $inventories = Inventory::with('warehouse')->get();

    return view('warehouse.incoming-asset-create', compact('inventories'));
}

public function storeIncomingAsset(Request $request)
{
    $request->validate([
        'inventory_id' => 'required|exists:inventories,id',
        'quantity_received' => 'required|integer|min:1',
        'source' => 'required|string|max:255',
        'received_at' => 'required|date'
    ]);

   $inventory = Inventory::findOrFail($request->inventory_id);

$warehouse = $inventory->warehouse;

$usedCapacity = $warehouse->inventories()
    ->get()
    ->sum(function ($item) {
        return $item->quantity * $item->volume_per_unit;
    });

$newStockVolume = $request->quantity_received * $inventory->volume_per_unit;

if (($usedCapacity + $newStockVolume) > $warehouse->total_capacity) {
    return back()->withErrors([
        'quantity_received' => 'This warehouse does not have enough remaining capacity.'
    ])->withInput();
}

IncomingAssetLog::create([
    'inventory_id' => $request->inventory_id,
    'quantity_received' => $request->quantity_received,
    'source' => $request->source,
    'received_at' => $request->received_at
]);

$inventory->increment('quantity', $request->quantity_received);
    return redirect()->route('warehouse.index')
        ->with('success', 'Incoming assets recorded and inventory updated successfully!');
}

public function createLocker()
{
    $warehouses = WarehouseHub::all();

    return view('warehouse.locker-create', compact('warehouses'));
}

public function storeLocker(Request $request)
{
    $request->validate([
        'warehouse_id' => 'required|exists:warehouse_hubs,id',
        'locker_code' => 'required|string|max:255|unique:storage_lockers,locker_code',
        'capacity' => 'required|numeric|min:0.001'
    ]);

    StorageLocker::create([
        'warehouse_id' => $request->warehouse_id,
        'locker_code' => $request->locker_code,
        'capacity' => $request->capacity
    ]);

    return redirect()->route('warehouse.index')
        ->with('success', 'Storage locker added successfully!');
}

public function createLockerAssignment()
{
    $lockers = StorageLocker::with('warehouse')->get();

    $inventories = Inventory::with('warehouse')->get();

    return view('warehouse.locker-assignment-create', compact(
        'lockers',
        'inventories'
    ));
}

public function storeLockerAssignment(Request $request)
{
    $request->validate([
        'locker_id' => 'required|exists:storage_lockers,id',
        'inventory_id' => 'required|exists:inventories,id',
        'quantity_assigned' => 'required|integer|min:1'
    ]);

    $locker = StorageLocker::findOrFail($request->locker_id);

    $inventory = Inventory::findOrFail($request->inventory_id);

    // Check whether the locker and inventory belong to the same warehouse.
    if ($locker->warehouse_id != $inventory->warehouse_id) {
        return back()->withErrors([
            'inventory_id' => 'The selected inventory must belong to the same warehouse as the locker.'
        ])->withInput();
    }

    // Calculate how much of this inventory is already assigned.
    $alreadyAssigned = LockerAssignment::where('inventory_id', $inventory->id)
        ->sum('quantity_assigned');

    $availableQuantity = $inventory->quantity - $alreadyAssigned;

    if ($request->quantity_assigned > $availableQuantity) {
        return back()->withErrors([
            'quantity_assigned' => 'Not enough unassigned inventory available.'
        ])->withInput();
    }

    // Calculate the current occupied volume of the locker.
    $usedCapacity = $locker->assignments()
        ->with('inventory')
        ->get()
        ->sum(function ($assignment) {
            return $assignment->quantity_assigned * $assignment->inventory->volume_per_unit;
        });

    // Calculate the volume required by this new assignment.
    $requiredCapacity = $request->quantity_assigned * $inventory->volume_per_unit;

    if (($usedCapacity + $requiredCapacity) > $locker->capacity) {
        return back()->withErrors([
            'quantity_assigned' => 'This locker does not have enough remaining capacity.'
        ])->withInput();
    }

    LockerAssignment::create([
        'locker_id' => $request->locker_id,
        'inventory_id' => $request->inventory_id,
        'quantity_assigned' => $request->quantity_assigned
    ]);

    return redirect()->route('warehouse.index')
        ->with('success', 'Inventory assigned to storage locker successfully!');
}

}