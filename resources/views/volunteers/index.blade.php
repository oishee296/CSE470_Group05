@extends('layouts.app')

@section('title', 'Volunteer Management - R17')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Volunteer Management (R17)</h2>
    <div>
        <a href="{{ route('volunteers.vetting') }}" class="btn btn-outline-primary">Vetting</a>
        <a href="{{ route('volunteers.roster') }}" class="btn btn-primary">Deployment Roster</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Skills</th>
                        <th>Certification</th><th>Registration</th><th>Assignments</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($volunteers as $volunteer)
                        <tr>
                            <td>{{ $volunteer->id }}</td>
                            <td>{{ $volunteer->full_name }}</td>
                            <td>{{ $volunteer->email }}</td>
                            <td>{{ $volunteer->phone_number }}</td>
                            <td>{{ $volunteer->skills }}</td>
                            <td>
                                <span class="badge {{ $volunteer->certification_status === 'Verified' ? 'bg-success' : ($volunteer->certification_status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $volunteer->certification_status }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $volunteer->registration_status === 'Approved' ? 'bg-success' : ($volunteer->registration_status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $volunteer->registration_status }}</span>
                            </td>
                            <td>{{ $volunteer->duty_assignments_count }}</td>
                            <td><a href="{{ route('volunteers.show', $volunteer) }}" class="btn btn-sm btn-info">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No volunteers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
