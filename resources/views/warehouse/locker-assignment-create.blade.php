@extends('layouts.app')

@section('title', 'Assign Inventory to Locker')

@section('content')

<div class="container py-5">
    <h1 class="mb-2">Assign Inventory to Storage Locker</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="card shadow-sm p-4">
        <form action="{{ route('locker-assignment.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Select Storage Locker</label>
                <select name="locker_id" class="form-select" required>
                    <option value="">Choose a locker</option>

                    @foreach($lockers as $locker)
                        <option value="{{ $locker->id }}"
                            {{ old('locker_id') == $locker->id ? 'selected' : '' }}>
                            {{ $locker->locker_code }} - {{ $locker->warehouse->name }}
                        </option>
                    @endforeach
                </select>

                @error('locker_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Select Inventory Item</label>
                <select name="inventory_id" class="form-select" required>
                    <option value="">Choose an inventory item</option>

                    @foreach($inventories as $inventory)
                        <option value="{{ $inventory->id }}"
                            {{ old('inventory_id') == $inventory->id ? 'selected' : '' }}>
                            {{ $inventory->item_name }} -
                            {{ $inventory->warehouse->name }} -
                            Available: {{ $inventory->quantity }} {{ $inventory->unit }}
                        </option>
                    @endforeach
                </select>

                @error('inventory_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity to Assign</label>
                <input type="number" name="quantity_assigned"
                    class="form-control" min="1"
                    value="{{ old('quantity_assigned') }}" required>

                @error('quantity_assigned')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Assign Inventory
            </button>

            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>

@endsection