@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h3>Welcome, {{ auth()->user()->name }}</h3>
<p class="text-muted">Role: {{ str_replace('_', ' ', auth()->user()->role) }}</p>


<div class="row g-3">
    @if (auth()->user()->role === 'citizen')
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5>Family Vulnerability Profile</h5>
                    <p>Tell us your household size and vulnerable members.</p>
                    <a href="{{ route('family.show') }}" class="btn btn-danger">Open</a>
                </div>
            </div>
        </div>
    @elseif (auth()->user()->role === 'logistics_manager')
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5>Warehouse Hub Management</h5>
                    <p>Manage warehouses, inventory, incoming assets and storage lockers.</p>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-danger">Open</a>
                </div>
            </div>
        </div>

    @elseif (auth()->user()->role === 'donor')
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5>Material Donations</h5>
                    <p>Register and track your material supply donations.</p>
                    <a href="{{ route('donations.index') }}" class="btn btn-success">
                        Open
                    </a>
                </div>
            </div>
        </div>

    @else
        <div class="col-12">
            <div class="alert alert-info">
                Your role's features will appear here as they are built.
            </div>
        </div>
    @endif
</div>

@endsection