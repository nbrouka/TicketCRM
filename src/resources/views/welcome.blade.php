@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Welcome</h3>
                </div>
                <div class="card-body">
                    <p class="text-center">Welcome to TicketCRM</p>
                    <div class="d-grid gap-2">
                        <a href="/login" class="btn btn-primary">Login</a>
                        <a href="/register" class="btn btn-secondary">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
