@extends('layouts.app')

@section('title', 'Incident Escalation - R19')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Incident Escalation Engine (R19)</h2>
    <form action="{{ route('escalations.run') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Run Escalation Check</button>
    </form>
</div>

<div class="alert alert-info small">
    Automatic escalation is based on waiting time for unresolved requests: &lt;2h no change, 2-&lt;4h +1 level, 4-&lt;6h +2 levels, 6h+ Urgent. Resolved requests are never auto-escalated. CSV timestamps are preserved (historical demonstration data), so overdue requests may already show long waiting times.
</div>

<div class="card mb-3">
    <div class="card-header">Filters</div>
    <div class="card-body">
        <form method="GET" action="{{ route('escalations.index') }}" class="row g-3">
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
                        <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->area_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['Pending','In Progress','Resolved'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All</option>
                    @foreach(['Low','Medium','High','Urgent'] as $p)
                        <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('escalations.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Emergency Requests</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Request Type</th>
                    <th>Zone</th>
                    <th>Description</th>
                    <th>Submitted Time</th>
                    <th>Waiting Duration</th>
                    <th>Current Priority</th>
                    <th>Previous Priority</th>
                    <th>Status</th>
                    <th>Last Escalation</th>
                    <th>Manual Priority Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    @php
                        $hours = intdiv($req->waiting_minutes, 60);
                        $mins = $req->waiting_minutes % 60;
                        $pBadge = match($req->priority) {
                            'Urgent' => 'bg-danger',
                            'High' => 'bg-warning text-dark',
                            'Medium' => 'bg-info text-dark',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->request_type }}</td>
                        <td>{{ $req->zone->area_name ?? '-' }}</td>
                        <td>{{ $req->description }}</td>
                        <td>{{ \Carbon\Carbon::parse($req->created_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $hours }}h {{ $mins }}m {{ $req->status === 'Resolved' ? '(at resolution)' : '' }}</td>
                        <td><span class="badge {{ $pBadge }}">{{ $req->priority }}</span></td>
                        <td>{{ $req->previous_priority ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $req->status === 'Resolved' ? 'bg-success' : ($req->status === 'In Progress' ? 'bg-primary' : 'bg-secondary') }}">{{ $req->status }}</span>
                        </td>
                        <td>{{ $req->last_escalated_at ? \Carbon\Carbon::parse($req->last_escalated_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if($req->status !== 'Resolved')
                                <form action="{{ route('escalations.priority', $req) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="priority" class="form-select form-select-sm" style="min-width:110px">
                                        @foreach(['Low','Medium','High','Urgent'] as $p)
                                            <option value="{{ $p }}" {{ $req->priority == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary">Update</button>
                                </form>
                            @else
                                <span class="text-muted">Resolved (no changes)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center">No emergency requests found. Import R19 CSV dataset first.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
