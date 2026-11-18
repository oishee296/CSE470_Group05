@extends('layouts.app')

@section('title', 'Audit Details - R20')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Audit Record Details</h2>
    <a href="{{ route('audit.index') }}" class="btn btn-secondary">Back to Audit Log</a>
</div>

<div class="card">
    <div class="card-header">Record #{{ $auditTrail->id }} (read-only)</div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Action Type</dt><dd class="col-sm-9">{{ $auditTrail->action_type }}</dd>
            <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{{ $auditTrail->description }}</dd>
            <dt class="col-sm-3">Related Type</dt><dd class="col-sm-9">{{ $auditTrail->related_type ?? '-' }}</dd>
            <dt class="col-sm-3">Related ID</dt><dd class="col-sm-9">{{ $auditTrail->related_id ?? '-' }}</dd>
            <dt class="col-sm-3">Quantity</dt><dd class="col-sm-9">{{ $auditTrail->quantity ?? '-' }}</dd>
            <dt class="col-sm-3">Performed By</dt><dd class="col-sm-9">{{ $auditTrail->performed_by ?? '-' }}</dd>
            <dt class="col-sm-3">Date &amp; Time</dt><dd class="col-sm-9">{{ $auditTrail->created_at ? \Carbon\Carbon::parse($auditTrail->created_at)->format('Y-m-d H:i:s') : '-' }}</dd>
        </dl>
    </div>
</div>
@endsection
