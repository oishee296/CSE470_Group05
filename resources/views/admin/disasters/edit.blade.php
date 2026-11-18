@extends('layouts.app')

@section('title', 'Edit Disaster - R16')

@section('content')
<h2 class="mb-3">Edit Disaster</h2>
<div class="card">
    <div class="card-body">
        <form action="{{ route('disasters.update', $disaster) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Disaster Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $disaster->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Disaster Type</label>
                    <select name="type" class="form-select" required>
                        @foreach(['Flood','Cyclone','Earthquake','Landslide','Fire','Other'] as $type)
                            <option value="{{ $type }}" {{ old('type', $disaster->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $disaster->description) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $disaster->start_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date (optional)</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $disaster->end_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['Active','Completed','Inactive'] as $status)
                            <option value="{{ $status }}" {{ old('status', $disaster->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Disaster</button>
                <a href="{{ route('disasters.show', $disaster) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
