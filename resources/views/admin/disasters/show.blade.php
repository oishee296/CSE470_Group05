@extends('layouts.app')

@section('title', 'Disaster Details - R16')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Disaster Details</h2>
    <div>
        <a href="{{ route('disasters.edit', $disaster) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('disasters.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Disaster Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6"><strong>Name:</strong> {{ $disaster->name }}</div>
            <div class="col-md-6"><strong>Type:</strong> {{ $disaster->type }}</div>
            <div class="col-md-6"><strong>Start Date:</strong> {{ $disaster->start_date?->format('Y-m-d') }}</div>
            <div class="col-md-6"><strong>End Date:</strong> {{ $disaster->end_date?->format('Y-m-d') ?? '-' }}</div>
            <div class="col-md-6"><strong>Status:</strong> {{ $disaster->status }}</div>
            <div class="col-12 mt-2"><strong>Description:</strong><br>{{ $disaster->description ?? '-' }}</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Add Affected Zone</div>
    <div class="card-body">
        <form action="{{ route('disasters.zones.store', $disaster) }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">District</label>
                    <input type="text" name="district" class="form-control" value="{{ old('district') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sub-district / Upazila</label>
                    <input type="text" name="sub_district" class="form-control" value="{{ old('sub_district') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Area Name</label>
                    <input type="text" name="area_name" class="form-control" value="{{ old('area_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Severity Level</label>
                    <select name="severity_level" class="form-select" required>
                        <option value="">Select</option>
                        @foreach(['Low','Medium','High','Critical'] as $level)
                            <option value="{{ $level }}" {{ old('severity_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Priority Tier</label>
                    <select name="priority_tier" class="form-select" required>
                        <option value="">Select</option>
                        @foreach(['Low','Medium','High','Urgent'] as $tier)
                            <option value="{{ $tier }}" {{ old('priority_tier') == $tier ? 'selected' : '' }}>{{ $tier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Add Zone</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Affected Zones</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>District</th>
                        <th>Sub-district</th>
                        <th>Area</th>
                        <th>Severity</th>
                        <th>Priority Tier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disaster->zones as $zone)
                        <tr>
                            <form action="{{ route('disasters.zones.update', [$disaster, $zone]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td>{{ $zone->id }}</td>
                                <td><input type="text" name="district" class="form-control form-control-sm" value="{{ $zone->district }}" required></td>
                                <td><input type="text" name="sub_district" class="form-control form-control-sm" value="{{ $zone->sub_district }}" required></td>
                                <td><input type="text" name="area_name" class="form-control form-control-sm" value="{{ $zone->area_name }}" required></td>
                                <td>
                                    <select name="severity_level" class="form-select form-select-sm" required>
                                        @foreach(['Low','Medium','High','Critical'] as $level)
                                            <option value="{{ $level }}" {{ $zone->severity_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="priority_tier" class="form-select form-select-sm" required>
                                        @foreach(['Low','Medium','High','Urgent'] as $tier)
                                            <option value="{{ $tier }}" {{ $zone->priority_tier == $tier ? 'selected' : '' }}>{{ $tier }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </form>
                                        <form action="{{ route('disasters.zones.destroy', [$disaster, $zone]) }}" method="POST" onsubmit="return confirm('Delete this zone?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No zones added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
