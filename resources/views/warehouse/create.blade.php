@extends('layouts.app')

@section('title', 'Add Warehouse')

@section('content')

<div class="container py-3">

    <h1 class="mb-2">Add New Warehouse</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="card shadow-sm p-4">

        <form action="{{ route('warehouse.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label class="form-label">Warehouse Name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Enter warehouse name"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>
                <input
                    type="text"
                    name="location"
                    class="form-control"
                    placeholder="Enter warehouse location"
                    value="{{ old('location') }}"
                    required
                >
                @error('location')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Total Capacity (m³)</label>
                <input
                    type="number"
                    name="total_capacity"
                    class="form-control"
                    placeholder="Enter total capacity"
                    step="0.01"
                    min="0.01"
                    value="{{ old('total_capacity') }}"
                    required
                >
                @error('total_capacity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Add Warehouse
            </button>

            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

@endsection