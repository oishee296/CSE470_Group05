<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Warehouse - SHARE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

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

</body>
</html>