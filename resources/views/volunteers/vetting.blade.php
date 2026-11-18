@extends('layouts.app')

@section('title', 'Volunteer Vetting - R17')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Volunteer Vetting</h2>
    <div>
        <a href="{{ route('volunteers.index') }}" class="btn btn-secondary">All Volunteers</a>
        <a href="{{ route('volunteers.roster') }}" class="btn btn-primary">Deployment Roster</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th><th>Skills</th><th>Certification</th><th>Cert. Status</th><th>Registration</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($volunteers as $volunteer)
                        <tr>
                            <td>
                                <strong>{{ $volunteer->full_name }}</strong><br>
                                <small>{{ $volunteer->email }} | {{ $volunteer->phone_number }}</small>
                            </td>
                            <td>{{ $volunteer->skills }}</td>
                            <td>{{ $volunteer->certification_name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('volunteers.certification', $volunteer) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    <select name="certification_status" class="form-select form-select-sm">
                                        @foreach(['Pending','Verified','Rejected'] as $s)
                                            <option value="{{ $s }}" {{ $volunteer->certification_status == $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                            <td><span class="badge {{ $volunteer->registration_status === 'Approved' ? 'bg-success' : ($volunteer->registration_status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $volunteer->registration_status }}</span></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('volunteers.show', $volunteer) }}" class="btn btn-sm btn-info">Profile</a>
                                    <form action="{{ route('volunteers.approve', $volunteer) }}" method="POST">@csrf <button class="btn btn-sm btn-success">Approve</button></form>
                                    <form action="{{ route('volunteers.reject', $volunteer) }}" method="POST">@csrf <button class="btn btn-sm btn-danger">Reject</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No volunteer registrations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
