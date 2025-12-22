@extends('tickets.layout')

@section('title', 'Tickets - TicketCRM')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tickets</li>
@endsection

@section('ticket-card-title', 'Tickets Management')

@section('ticket-card-tools')
    <!-- Additional tools can be added here if needed -->
@endsection

@section('ticket-content')
    @include('tickets.partials.filter')
    @include('tickets.partials.table')
    @include('tickets.partials.pagination')
@endsection

@section('ticket-scripts')
    <script src="{{ asset('js/tickets.js') }}"></script>
@endsection
