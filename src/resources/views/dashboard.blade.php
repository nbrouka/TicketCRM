@extends('layouts.dashboard')

@section('title', 'Dashboard - TicketCRM')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Overview</h3>
                    </div>
                    <div class="card-body">
                        <p>Welcome to your TicketCRM dashboard!</p>
                        <p>Select an option from the sidebar to manage tickets.</p>

                        <!-- Ticket Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i
                                            class="fas fa-calendar-day"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Today</span>
                                        <span class="info-box-number" id="tickets-today">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success elevation-1"><i
                                            class="fas fa-calendar-week"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">This Week</span>
                                        <span class="info-box-number" id="tickets-week">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">This Month</span>
                                        <span class="info-box-number" id="tickets-month">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary elevation-1"><i
                                            class="fas fa-ticket-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Tickets</span>
                                        <span class="info-box-number" id="tickets-total">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Statistics Chart -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4>Daily Ticket Statistics</h4>
                                        <div class="form-group mb-0">
                                            <label for="monthSelector" class="mr-2">Select Month:</label>
                                            <select id="monthSelector" class="form-control d-inline-block w-auto">
                                                <option value="last_month">Last Month</option>
                                                <option value="current_month">Current Month</option>
                                                <option value="previous_2">2 Months Ago</option>
                                                <option value="previous_3">3 Months Ago</option>
                                                <option value="previous_4">4 Months Ago</option>
                                                <option value="previous_5">5 Months Ago</option>
                                                <option value="previous_6">6 Months Ago</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="monthlyDashboardChart"
                                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Status Distribution Chart -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4>Ticket Status Distribution</h4>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="statusDistributionChart"
                                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        @vite(['resources/js/dashboard.js'])
    @endsection
