<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Incoming Assets - SHARE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <h1 class="mb-2">Record Incoming Assets</h1>
    <p class="text-muted mb-4">SHARE - Disaster Relief Management System</p>

    <div class="card shadow-sm p-4">

        <form action="{{ route('incoming-asset.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label class="form-label">Select Inventory Item</label>

                <select name="inventory_id" class="form-select" required>
                    <option value="">Choose an inventory item</option>

                    @foreach($inventories as $item)
                        <option value="{{ $item->id }}"
                            {{ old('inventory_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->item_name }} ({{ $item->warehouse->name }})
                        </option>
                    @endforeach

                </select>

                @error('inventory_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity Received</label>

                <input
                    type="number"
                    name="quantity_received"
                    class="form-control"
                    min="1"
                    value="{{ old('quantity_received') }}"
                    placeholder="Enter quantity received"
                    required
                >

                @error('quantity_received')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Source</label>

                <input
                    type="text"
                    name="source"
                    class="form-control"
                    value="{{ old('source') }}"
                    placeholder="Example: Local Supplier"
                    required
                >

                @error('source')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Received Date and Time</label>

                <input
                    type="datetime-local"
                    name="received_at"
                    class="form-control"
                    value="{{ old('received_at') }}"
                    required
                >

                @error('received_at')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Record Incoming Assets
            </button>

            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>