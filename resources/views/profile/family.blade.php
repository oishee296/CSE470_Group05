@extends('layouts.app')
@section('title', 'Family Vulnerability Profile')
@section('content')
@php
    $badge = ['low' => 'success', 'medium' => 'warning', 'high' => 'orange', 'critical' => 'danger'];
@endphp

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>{{ $profile ? 'Update' : 'Create' }} Family Profile</h4>
                <form method="POST" action="{{ route('family.save') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Region</label>
                        <select name="area" class="form-select @error('area') is-invalid @enderror" required>
                            <option value="">-- select --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area }}" @selected(old('area', $profile->area ?? '') === $area)>{{ $area }}</option>
                            @endforeach
                        </select>
                        @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @foreach ([
                        'household_size' => 'Household size (total members)',
                        'infants_count' => 'Infants (under 2)',
                        'elderly_count' => 'Elderly (65+)',
                        'injured_count' => 'Injured members',
                    ] as $field => $label)
                        <div class="mb-3">
                            <label class="form-label">{{ $label }}</label>
                            <input type="number" name="{{ $field }}"
                                   min="{{ $field === 'household_size' ? 1 : 0 }}"
                                   value="{{ old($field, $profile->$field ?? ($field === 'household_size' ? 1 : 0)) }}"
                                   class="form-control @error($field) is-invalid @enderror" required>
                            @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endforeach

                    <button class="btn btn-danger btn-lg w-100">Save Profile</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @if ($profile)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5>Your Severity</h5>
                    <p class="fs-5 mb-1">Weight: <strong>{{ $profile->severity_weight }}</strong></p>
                    <span class="badge fs-6 text-bg-{{ $badge[$profile->severity_level] ?? 'secondary' }}">
                        {{ strtoupper($profile->severity_level) }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5>{{ $profile->area }} Region Summary</h5>
                    <table class="table table-sm mb-0">
                        <tr><td>Registered households</td><td>{{ $regional->households }}</td></tr>
                        <tr><td>Total people</td><td>{{ $regional->people }}</td></tr>
                        <tr><td>Infants</td><td>{{ $regional->infants }}</td></tr>
                        <tr><td>Elderly</td><td>{{ $regional->elderly }}</td></tr>
                        <tr><td>Injured</td><td>{{ $regional->injured }}</td></tr>
                        <tr class="table-danger"><td><strong>Regional severity weight</strong></td><td><strong>{{ $regional->total_weight }}</strong></td></tr>
                    </table>
                </div>
            </div>

            <form method="POST" action="{{ route('family.destroy') }}"
                  onsubmit="return confirm('Delete your family profile?');">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Delete Profile</button>
            </form>
        @else
            <div class="alert alert-info">No profile yet. Fill in the form to generate your severity weight.</div>
        @endif
    </div>
</div>
@endsection