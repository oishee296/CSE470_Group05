@extends('layouts.app')

@section('title', 'Disaster Management - R16')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Disaster Management (R16)</h2>
    <a href="{{ route('disasters.create') }}" class="btn btn-primary">Create Disaster</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Zones</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disasters as $disaster)
                        <tr>
                            <td>{{ $disaster->id }}</td>
                            <td>{{ $disaster->name }}</td>
                            <td>{{ $disaster->type }}</td>
                            <td>{{ $disaster->start_date?->format('Y-m-d') }}</td>
                            <td>{{ $disaster->end_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $disaster->status === 'Active' ? 'bg-success' : ($disaster->status === 'Completed' ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ $disaster->status }}</span>
                            </td>
                            <td>{{ $disaster->zones_count }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('disasters.show', $disaster) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('disasters.edit', $disaster) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('disasters.destroy', $disaster) }}" method="POST" onsubmit="return confirm('Delete this disaster? If it has dependent records it will be marked inactive.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No disasters found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
