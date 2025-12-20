@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">@yield('auth-title')</h3>
                </div>
                <div class="card-body">
                    @yield('auth-content')
                    <div class="mt-3 text-center">
                        @yield('auth-links')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
