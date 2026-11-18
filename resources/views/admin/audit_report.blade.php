@extends('layouts.app')

@section('title', 'Audit & Transparency - R20')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Audit &amp; Transparency Logs (R20)</h2>
    <a href="{{ route('donations.index') }}" class="btn btn-primary">Record Donation / Operation</a>
</div>

<div class="alert alert-secondary small">
    Audit records are application-level immutable: this module provides view/filter/details only (no edit or delete actions). Important operations in the system create audit records automatically.
</div>

<div class="card mb-3">
    <div class="card-header">Filters</div>
    <div class="card-body">
        <form method="GET" action="{{ route('audit.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Action Type</label>
                <select name="action_type" class="form-select">
                    <option value="">All</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Related Record Type</label>
                <select name="related_type" class="form-select">
                    <option value="">All</option>
                    @foreach($relatedTypes as $type)
                        <option value="{{ $type }}" {{ request('related_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('audit.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Audit Trail</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Action Type</th>
                        <th>Description</th>
                        <th>Related Record</th>
                        <th>Quantity</th>
                        <th>Performed By</th>
                        <th>Date &amp; Time</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                        <tr>
                            <td>{{ $audit->id }}</td>
                            <td><span class="badge bg-dark">{{ $audit->action_type }}</span></td>
                            <td>{{ $audit->description }}</td>
                            <td>{{ $audit->related_type ? $audit->related_type . ' #' . $audit->related_id : '-' }}</td>
                            <td>{{ $audit->quantity ?? '-' }}</td>
                            <td>{{ $audit->performed_by ?? '-' }}</td>
                            <td>{{ $audit->created_at ? \Carbon\Carbon::parse($audit->created_at)->format('Y-m-d H:i:s') : '-' }}</td>
                            <td><a href="{{ route('audit.show', $audit) }}" class="btn btn-sm btn-info">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No audit records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $audits->links() }}</div>
    </div>
</div>
@endsection
