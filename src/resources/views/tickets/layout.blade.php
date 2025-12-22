@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">@yield('ticket-card-title', 'Ticket Management')</h3>
                            <div class="card-tools">
                                @yield('ticket-card-tools')
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @yield('ticket-content')
                    </div>

                    <div class="card-footer">
                        @yield('ticket-footer')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @yield('ticket-scripts')
@endsection
