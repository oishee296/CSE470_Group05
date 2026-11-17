@extends('layouts.app')

@section('title', 'Add Inventory')

@section('content')

<div class="container py-3">

    <h1 class="mb-2">Add Inventory Item</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="card shadow-sm p-4">

        <form action="{{ route('inventory.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label class="form-label">Select Warehouse</label>

                <select name="warehouse_id" class="form-select" required>
                    <option value="">Choose a warehouse</option>

                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}"
                            {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach

                </select>

                @error('warehouse_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Item Name</label>

                <input
                    type="text"
                    name="item_name"
                    class="form-control"
                    placeholder="Example: Rice, Water, Medicine"
                    value="{{ old('item_name') }}"
                    required
                >

                @error('item_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    min="0"
                    value="{{ old('quantity', 0) }}"
                    required
                >

                @error('quantity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Unit</label>

                <select name="unit" class="form-select" required>
                    <option value="">Choose a unit</option>
                    <option value="kg">Kilogram (kg)</option>
                    <option value="litre">Litre</option>
                    <option value="packet">Packet</option>
                    <option value="box">Box</option>
                    <option value="piece">Piece</option>
                </select>

                @error('unit')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Volume Per Unit (m³)</label>

                <input
                    type="number"
                    name="volume_per_unit"
                    class="form-control"
                    step="0.001"
                    min="0.001"
                    placeholder="Example: 0.025"
                    value="{{ old('volume_per_unit') }}"
                    required
                >

                @error('volume_per_unit')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Add Inventory Item
            </button>

            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

@endsection