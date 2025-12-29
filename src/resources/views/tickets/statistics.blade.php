@extends('tickets.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Ticket Statistics by Day and Status</h3>
                            <div class="form-group mb-0">
                                <label for="monthSelectorStats" class="mr-2">Select Month:</label>
                                <select id="monthSelectorStats" class="form-control d-inline-block w-auto">
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
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyStatisticsChart"
                                style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detailed Statistics Table</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered" id="statisticsTable">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>New Tickets</th>
                                    <th>In Progress Tickets</th>
                                    <th>Done Tickets</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/statistics.js') }}"></script>
@endpush>
