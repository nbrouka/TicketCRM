<!-- Ticket Information -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="id">Ticket ID</label>
            <input type="text" class="form-control" id="id" value="#{{ $ticket->id }}" readonly>
        </div>

        <div class="form-group">
            <label for="theme">Theme</label>
            <input type="text" class="form-control" id="theme" value="{{ $ticket->theme }}" readonly>
        </div>

        <div class="form-group">
            <label>Status</label>
            <div class="input-group">
                <span class="form-control">
                    <span
                        class="badge {{ $ticket->status->value === 'new'
                            ? 'text-bg-info'
                            : ($ticket->status->value === 'in_progress'
                                ? 'text-bg-warning'
                                : 'text-bg-success') }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status->value)) }}
                    </span>
                </span>
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                        Change Status
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="created_at">Created At</label>
            <input type="text" class="form-control" id="created_at"
                value="{{ $ticket->created_at->format('Y-m-d H:i') }}" readonly>
        </div>

        <!-- Status Change Modal -->
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel">Change Ticket Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('tickets.updateStatus', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Select New Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="new" {{ $ticket->status->value === 'new' ? 'selected' : '' }}>
                                        New
                                    </option>
                                    <option value="in_progress"
                                        {{ $ticket->status->value === 'in_progress' ? 'selected' : '' }}>
                                        In Progress
                                    </option>
                                    <option value="done" {{ $ticket->status->value === 'done' ? 'selected' : '' }}>
                                        Done
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="date_answer">Date Answer</label>
            <input type="text" class="form-control" id="date_answer"
                value="{{ $ticket->date_answer ? $ticket->date_answer->format('Y-m-d H:i') : 'N/A' }}" readonly>
        </div>
    </div> <!-- Close first col-md-6 -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="customer_name">Customer Name</label>
            <input type="text" class="form-control" id="customer_name"
                value="{{ $ticket->customer->name ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
            <label for="customer_email">Customer Email</label>
            <input type="email" class="form-control" id="customer_email"
                value="{{ $ticket->customer->email ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
            <label for="customer_phone">Customer Phone</label>
            <input type="text" class="form-control" id="customer_phone"
                value="{{ $ticket->customer->phone ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
            <label for="ticket_text">Ticket Text</label>
            <textarea class="form-control" id="ticket_text" rows="5" readonly>{{ $ticket->text }}</textarea>
        </div>
    </div> <!-- Close second col-md-6 -->
</div> <!-- Close row -->
