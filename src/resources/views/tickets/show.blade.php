@extends('tickets.layout')

@section('title', 'Ticket Details - TicketCRM')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
    <li class="breadcrumb-item active">Ticket #{{ $ticket->id }}</li>
@endsection

@section('ticket-card-title', 'Ticket Details')

@section('ticket-card-tools')
    @include('tickets.partials.show-success-message')
@endsection

@section('ticket-content')
    @include('tickets.partials.show-ticket-details')
    @include('tickets.partials.show-attachments')
@endsection

@section('ticket-footer')
    @include('tickets.partials.show-footer')
@endsection
