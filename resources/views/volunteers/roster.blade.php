@extends('layouts.app')

@section('title', 'Volunteer Deployment Roster - R17')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Volunteer Deployment Roster</h2>
    <div>
        <a href="{{ route('volunteers.index') }}" class="btn btn-secondary">All Volunteers</a>
        <a href="{{ route('volunteers.vetting') }}" class="btn btn-outline-primary">Vetting</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Assign Approved Volunteer to Active Zone</div>
    <div class="card-body">
        <form action="{{ route('assignments.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Approved Volunteer</label>
                    <select name="volunteer_profile_id" class="form-select" required>
                        <option value="">Select volunteer</option>
                        @foreach($approvedVolunteers as $v)
                            <option value="{{ $v->id }}" {{ old('volunteer_profile_id') == $v->id ? 'selected' : '' }}>{{ $v->full_name }} ({{ $v->skills }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Zone (active disasters only)</label>
                    <select name="zone_id" class="form-select" required>
                        <option value="">Select zone</option>
                        @foreach($activeDisasters as $disaster)
                            @foreach($disaster->zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $disaster->name }} — {{ $zone->area_name }} ({{ $zone->district }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigned Responsibility</label>
                    <input type="text" name="assigned_responsibility" class="form-control" value="{{ old('assigned_responsibility') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assignment Date</label>
                    <input type="date" name="assignment_date" class="form-control" value="{{ old('assignment_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assignment Status</label>
                    <select name="assignment_status" class="form-select" required>
                        @foreach(['Assigned','Active','Completed','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('assignment_status','Assigned') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary">Create Assignment</button>
                </div>
            </div>
        </form>
        @if($approvedVolunteers->isEmpty())
            <div class="alert alert-info mt-3 mb-0">No approved volunteers available. Approve volunteers in the Vetting page first.</div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">Deployment Records (Which volunteers are assigned to which zones)</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Volunteer</th><th>Disaster</th><th>Zone</th><th>Responsibility</th><th>Date</th><th>Status</th><th>Update / Cancel</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ $a->volunteerProfile->full_name ?? '-' }}</td>
                        <td>{{ $a->disaster->name ?? '-' }}</td>
                        <td>{{ $a->zone->area_name ?? '-' }} ({{ $a->zone->district ?? '' }})</td>
                        <td>{{ $a->assigned_responsibility }}</td>
                        <td>{{ $a->assignment_date?->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $a->assignment_status === 'Completed' ? 'bg-success' : ($a->assignment_status === 'Cancelled' ? 'bg-secondary' : ($a->assignment_status === 'Active' ? 'bg-primary' : 'bg-info text-dark')) }}">{{ $a->assignment_status }}</span></td>
                        <td>
                            <form action="{{ route('assignments.update', $a) }}" method="POST" class="d-flex gap-1 flex-wrap">
                                @csrf @method('PUT')
                                <input type="text" name="assigned_responsibility" class="form-control form-control-sm" style="min-width:140px" value="{{ $a->assigned_responsibility }}" required>
                                <select name="assignment_status" class="form-select form-select-sm" style="min-width:130px" required>
                                    @foreach(['Assigned','Active','Completed','Cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $a->assignment_status==$s?'selected':'' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No deployment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
