@extends('layouts.auth')

@section('title', 'Register')
@section('auth-title', 'Register')

@section('auth-content')
<form method="POST" action="/register">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>
@endsection

@section('auth-links')
<p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
@endsection

@section('scripts')
@endsection
