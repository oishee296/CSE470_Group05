@extends('layouts.app')

@section('title', 'Volunteer Profile - R17')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Volunteer Profile</h2>
    <a href="{{ route('volunteers.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">Profile Details</div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $volunteer->full_name }}</p>
                <p><strong>Email:</strong> {{ $volunteer->email }}</p>
                <p><strong>Phone:</strong> {{ $volunteer->phone_number }}</p>
                <p><strong>Address:</strong> {{ $volunteer->address }}</p>
                <p><strong>Skills:</strong> {{ $volunteer->skills }}</p>
                <p><strong>Certification:</strong> {{ $volunteer->certification_name ?? '-' }} ({{ $volunteer->certification_status }})</p>
                <p><strong>Registration:</strong>
                    <span class="badge {{ $volunteer->registration_status === 'Approved' ? 'bg-success' : ($volunteer->registration_status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $volunteer->registration_status }}</span>
                </p>
                <div class="d-flex gap-2">
                    <form action="{{ route('volunteers.approve', $volunteer) }}" method="POST">@csrf <button class="btn btn-success btn-sm">Approve</button></form>
                    <form action="{{ route('volunteers.reject', $volunteer) }}" method="POST">@csrf <button class="btn btn-danger btn-sm">Reject</button></form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">Assign to Active Disaster Zone</div>
            <div class="card-body">
                @if($volunteer->registration_status !== 'Approved')
                    <div class="alert alert-warning mb-0">Only approved volunteers can be assigned. Approve this volunteer first.</div>
                @else
                    <form action="{{ route('assignments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="volunteer_profile_id" value="{{ $volunteer->id }}">
                        <div class="mb-2">
                            <label class="form-label">Zone (active disasters only)</label>
                            <select name="zone_id" class="form-select" required>
                                <option value="">Select zone</option>
                                @foreach($activeDisasters as $disaster)
                                    @foreach($disaster->zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $disaster->name }} — {{ $zone->area_name }} ({{ $zone->district }})</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Assigned Responsibility</label>
                            <input type="text" name="assigned_responsibility" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Assignment Date</label>
                            <input type="date" name="assignment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assignment Status</label>
                            <select name="assignment_status" class="form-select" required>
                                @foreach(['Assigned','Active','Completed','Cancelled'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">Assign Volunteer</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">Deployment Records</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light"><tr><th>Disaster</th><th>Zone</th><th>Responsibility</th><th>Date</th><th>Status</th><th>Update</th></tr></thead>
            <tbody>
                @forelse($volunteer->dutyAssignments as $a)
                    <tr>
                        <td>{{ $a->disaster->name ?? '-' }}</td>
                        <td>{{ $a->zone->area_name ?? '-' }}</td>
                        <td>{{ $a->assigned_responsibility }}</td>
                        <td>{{ $a->assignment_date?->format('Y-m-d') }}</td>
                        <td>{{ $a->assignment_status }}</td>
                        <td>
                            <form action="{{ route('assignments.update', $a) }}" method="POST" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <input type="text" name="assigned_responsibility" class="form-control form-control-sm" value="{{ $a->assigned_responsibility }}" required>
                                <select name="assignment_status" class="form-select form-select-sm" required>
                                    @foreach(['Assigned','Active','Completed','Cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $a->assignment_status==$s?'selected':'' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No assignments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
