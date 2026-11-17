@extends('layouts.app')
@section('title', 'Sign up')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Create your SHARE account</h4>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">I am a...</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="citizen" @selected(old('role') === 'citizen')>Affected Citizen</option>
                            <option value="donor" @selected(old('role') === 'donor')>Humanitarian Donor</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (min 8 characters)</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-danger w-100">Sign up</button>
                </form>
                <p class="mt-3 mb-0 text-center">Already registered? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection