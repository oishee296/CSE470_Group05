@extends('layouts.app')

@section('title', 'Warehouse Hub Management')

@section('content')


<div class="mb-4">
    <h5 class="mb-3">Quick Actions</h5>

    <a href="{{ route('warehouse.create') }}" class="btn btn-primary me-2 mb-2">
        Add Warehouse
    </a>

    <a href="{{ route('inventory.create') }}" class="btn btn-success me-2 mb-2">
        Add Inventory
    </a>

    <a href="{{ route('incoming-asset.create') }}" class="btn btn-info me-2 mb-2">
        Record Incoming Assets
    </a>

    <a href="{{ route('locker.create') }}" class="btn btn-warning me-2 mb-2">
        Add Locker
    </a>

    <a href="{{ route('locker-assignment.create') }}" class="btn btn-secondary me-2 mb-2">
        Assign Inventory to Locker
    </a>
</div>

    <h1 class="mb-2">Warehouse Hub Management</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Total Warehouses</h6>
                <h2>{{ $totalWarehouses }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Inventory Items</h6>
                <h2>{{ $totalInventoryItems }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Incoming Asset Records</h6>
                <h2>{{ $totalIncomingAssets }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Storage Lockers</h6>
                <h2>{{ $totalLockers }}</h2>
            </div>
        </div>

    </div>

    <div class="card shadow-sm p-4">

        <h4 class="mb-3">Warehouse List</h4>

        @if($warehouses->isEmpty())

            <p class="text-muted">No warehouses have been added yet.</p>

        @else

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Warehouse Name</th>
                        <th>Location</th>
                        <th>Total Capacity (m³)</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->id }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->location }}</td>
                            <td>{{ $warehouse->total_capacity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif

    </div>

    <div class="card shadow-sm mb-4">
    <div class="card-header">
        <h4 class="mb-0">Warehouse Capacity Overview</h4>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Warehouse</th>
                        <th>Total Capacity (m³)</th>
                        <th>Used Capacity (m³)</th>
                        <th>Remaining Capacity (m³)</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($warehouses as $warehouse)
                        @php
                            $usedCapacity = 0;

                            foreach($warehouse->inventories as $inventory) {
                                $usedCapacity +=
                                    $inventory->quantity * $inventory->volume_per_unit;
                            }

                            $remainingCapacity =
                                $warehouse->total_capacity - $usedCapacity;
                        @endphp

                        <tr>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ number_format($warehouse->total_capacity, 3) }}</td>
                            <td>{{ number_format($usedCapacity, 3) }}</td>
                            <td>{{ number_format($remainingCapacity, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

         <div class="card shadow-sm p-4 mt-4">

        <h4 class="mb-3">Inventory List</h4>

        @php
            $hasInventory = false;
        @endphp

        @foreach($warehouses as $warehouse)

            @if($warehouse->inventories->isNotEmpty())

                @php
                    $hasInventory = true;
                @endphp

                <h5 class="mt-3">{{ $warehouse->name }}</h5>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Volume Per Unit (m³)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($warehouse->inventories as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ $item->volume_per_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif

        @endforeach

        @if(!$hasInventory)
            <p class="text-muted">No inventory items have been added yet.</p>
        @endif

    </div>

    <div class="card shadow-sm mb-4">
    <div class="card-header">
        <h4 class="mb-0">Storage Locker List</h4>
    </div>

    <div class="card-body">
        @foreach($warehouses as $warehouse)
            <h5 class="mt-3">{{ $warehouse->name }}</h5>

            @if($warehouse->lockers->count() > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Locker ID</th>
                            <th>Locker Code</th>
                            <th>Total Capacity (m³)</th>
                            <th>Used Capacity (m³)</th>
                            <th>Remaining Capacity (m³)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($warehouse->lockers as $locker)

                        @php
                        $usedCapacity = $locker->assignments
                             ->sum(function ($assignment) {
                          return $assignment->quantity_assigned *
                           $assignment->inventory->volume_per_unit;
                            });

                        $remainingCapacity = $locker->capacity - $usedCapacity;
                       @endphp

                      <tr>
                         <td>{{ $locker->id }}</td>
                         <td>{{ $locker->locker_code }}</td>
                         <td>{{ number_format($locker->capacity, 3) }}</td>
                         <td>{{ number_format($usedCapacity, 3) }}</td>
                         <td>{{ number_format($remainingCapacity, 3) }}</td>
                      </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No storage lockers added yet.</p>
            @endif
        @endforeach
    </div>
</div>

    <div class="card shadow-sm mb-4">
    <div class="card-header">
        <h4 class="mb-0">Locker Assignment History</h4>
    </div>

    <div class="card-body">
        @if($lockerAssignments->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Assignment ID</th>
                        <th>Warehouse</th>
                        <th>Locker Code</th>
                        <th>Inventory Item</th>
                        <th>Quantity Assigned</th>
                        <th>Occupied Volume (m³)</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($lockerAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td>{{ $assignment->locker->warehouse->name }}</td>
                            <td>{{ $assignment->locker->locker_code }}</td>
                            <td>{{ $assignment->inventory->item_name }}</td>
                            <td>
                                {{ $assignment->quantity_assigned }}
                                {{ $assignment->inventory->unit }}
                            </td>
                            <td>
                                {{ number_format(
                                    $assignment->quantity_assigned * $assignment->inventory->volume_per_unit,
                                    3
                                ) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
         </div>
        @else
            <p class="text-muted">No locker assignments recorded yet.</p>
        @endif
    </div>
</div>

          <div class="card shadow-sm p-4 mt-4">

        <h4 class="mb-3">Incoming Asset History</h4>

        @if($incomingAssets->isEmpty())

            <p class="text-muted">No incoming assets have been recorded yet.</p>

        @else

            <table class="table table-bordered">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Warehouse</th>
                        <th>Item Name</th>
                        <th>Quantity Received</th>
                        <th>Source</th>
                        <th>Received Date</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($incomingAssets as $asset)
                        <tr>
                            <td>{{ $asset->id }}</td>
                            <td>{{ $asset->inventory->warehouse->name }}</td>
                            <td>{{ $asset->inventory->item_name }}</td>
                            <td>{{ $asset->quantity_received }} {{ $asset->inventory->unit }}</td>
                            <td>{{ $asset->source }}</td>
                            <td>{{ $asset->received_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

@endsection