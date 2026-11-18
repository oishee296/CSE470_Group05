@extends('layouts.app')

@section('title', 'Need Assessment - R18')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Real-Time Need Assessment Grid (R18)</h2>
</div>

<div class="card mb-3">
    <div class="card-header">Filters</div>
    <div class="card-body">
        <form method="GET" action="{{ route('assessment.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Disaster</label>
                <select name="disaster_id" class="form-select">
                    <option value="">All</option>
                    @foreach($disasters as $d)
                        <option value="{{ $d->id }}" {{ request('disaster_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Zone</label>
                <select name="zone_id" class="form-select">
                    <option value="">All</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->area_name }} ({{ $z->district }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Resource</label>
                <select name="resource_name" class="form-select">
                    <option value="">All</option>
                    @foreach($resources as $r)
                        <option value="{{ $r }}" {{ request('resource_name') == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Request Scope</label>
                <select name="scope" class="form-select">
                    <option value="active" {{ !$showAll ? 'selected' : '' }}>Active / Unresolved (operational)</option>
                    <option value="all" {{ $showAll ? 'selected' : '' }}>All requests</option>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('assessment.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Demand vs Inventory (calculated from MySQL)</div>
    <div class="card-body">
        <p class="text-muted small mb-2">Default view uses active/unresolved requests for operational demand. Switch scope to "All requests" to inspect all records. Shortage = max(Total Demand - Available Stock, 0), Surplus = max(Available Stock - Total Demand, 0).</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Disaster</th>
                        <th>Zone</th>
                        <th>Resource</th>
                        <th>Total Demand</th>
                        <th>Available Stock</th>
                        <th>Shortage</th>
                        <th>Surplus</th>
                        <th>Status Indicator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php $hasShortage = $row['shortage'] > 0; @endphp
                        <tr class="{{ $hasShortage ? 'table-danger' : 'table-success' }}">
                            <td>{{ $row['disaster']->name ?? '-' }}</td>
                            <td>{{ $row['zone']->area_name ?? '-' }} ({{ $row['zone']->district ?? '' }})</td>
                            <td>{{ $row['resource_name'] }}</td>
                            <td>{{ $row['total_demand'] }}</td>
                            <td>{{ $row['available_stock'] }}</td>
                            <td><strong>{{ $row['shortage'] }}</strong></td>
                            <td>{{ $row['surplus'] }}</td>
                            <td>
                                @if($hasShortage)
                                    <span class="badge bg-danger">Shortage</span>
                                @else
                                    <span class="badge bg-success">Sufficient / Surplus</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No assessment data found. Import CSV datasets first.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
