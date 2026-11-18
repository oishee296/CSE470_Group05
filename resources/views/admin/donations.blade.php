@extends('layouts.app')

@section('title', 'Record Donation / Operation - R20')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Record Donation / Relief Operation</h2>
    <a href="{{ route('audit.index') }}" class="btn btn-secondary">Back to Audit Log</a>
</div>

<div class="alert alert-info small">
    Recording an operation here creates a Donation record (where applicable) and an immutable audit trail entry. Supported operations: Donation received, warehouse inventory received, resources allocated to a disaster zone, relief goods dispatched, recipient confirms delivery.
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">New Operation Record</div>
            <div class="card-body">
                <form action="{{ route('donations.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Donor / Source Name</label>
                        <input type="text" name="donor_name" class="form-control" value="{{ old('donor_name') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Donation / Record Type</label>
                        <input type="text" name="donation_type" class="form-control" value="{{ old('donation_type') }}" placeholder="e.g. Cash, Material, Warehouse Receipt" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Resource Name</label>
                        <input type="text" name="resource_name" class="form-control" value="{{ old('resource_name') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="donation_date" class="form-control" value="{{ old('donation_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Operation / Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Received" {{ old('status')=='Received'?'selected':'' }}>Donation Received / Warehouse Inventory Received</option>
                            <option value="Allocated" {{ old('status')=='Allocated'?'selected':'' }}>Resources Allocated to Disaster Zone</option>
                            <option value="Dispatched" {{ old('status')=='Dispatched'?'selected':'' }}>Relief Goods Dispatched</option>
                            <option value="Delivered" {{ old('status')=='Delivered'?'selected':'' }}>Recipient Confirms Delivery</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Save &amp; Create Audit Log</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">Recorded Donations / Operations</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Donor/Source</th><th>Type</th><th>Resource</th><th>Qty</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $d)
                            <tr>
                                <td>{{ $d->id }}</td>
                                <td>{{ $d->donor_name }}</td>
                                <td>{{ $d->donation_type }}</td>
                                <td>{{ $d->resource_name }}</td>
                                <td>{{ $d->quantity }}</td>
                                <td>{{ $d->donation_date?->format('Y-m-d') }}</td>
                                <td><span class="badge bg-secondary">{{ $d->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No donation/operation records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
