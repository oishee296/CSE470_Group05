<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Storage Locker - SHARE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <h1 class="mb-2">Add Storage Locker</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="card shadow-sm p-4">

        <form action="{{ route('locker.store') }}" method="POST">

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
                <label class="form-label">Locker Code</label>

                <input
                    type="text"
                    name="locker_code"
                    class="form-control"
                    placeholder="Example: L-01"
                    value="{{ old('locker_code') }}"
                    required
                >

                @error('locker_code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Locker Capacity (m³)</label>

                <input
                    type="number"
                    name="capacity"
                    class="form-control"
                    step="0.001"
                    min="0.001"
                    placeholder="Example: 10"
                    value="{{ old('capacity') }}"
                    required
                >

                @error('capacity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Add Storage Locker
            </button>

            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>