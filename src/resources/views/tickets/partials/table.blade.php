<!-- Results Summary -->
<div class="border-bottom pb-2 mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Showing {{ $tickets->count() }} tickets</h5>
        </div>
    </div>
</div>

<!-- Tickets Table -->
<div class="table-responsive p-0">
    <table class="table table-hover text-nowrap" id="tickets-table">
        <thead class="table-light">
            <tr>
                <th>Actions</th>
                <th>ID</th>
                <th>Status</th>
                <th>Customer</th>
                <th class="mobile-hidden">Email</th>
                <th class="mobile-hidden">Date Answer</th>
                <th class="mobile-hidden">Created At</th>
                <th>Theme</th>
                <th class="mobile-hidden">Ticket Text</th>
                <th>Files</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary"
                                title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary status-badge">#{{ $ticket->id }}</span></td>
                    <td>
                        <span
                            class="badge {{ $ticket->status->value === 'new'
                                ? 'text-bg-info'
                                : ($ticket->status->value === 'in_progress'
                                    ? 'text-bg-warning'
                                    : 'text-bg-success') }} status-badge">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status->value)) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                {{ $ticket->customer->name ?? 'N/A' }}
                            </div>
                        </div>
                    </td>
                    <td class="mobile-hidden">{{ $ticket->customer->email ?? 'N/A' }}</td>
                    <td class="mobile-hidden">
                        {{ $ticket->date_answer ? $ticket->date_answer->format('Y-m-d H:i') : 'N/A' }}
                    </td>
                    <td class="mobile-hidden">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-truncate" style="max-width: 150px;" title="{{ $ticket->theme }}">
                        {{ $ticket->theme }}
                    </td>
                    <td class="mobile-hidden text-truncate" style="max-width: 200px;" title="{{ $ticket->text }}">
                        {{ strlen($ticket->text) > 50 ? substr($ticket->text, 0, 50) . '...' : $ticket->text }}
                    </td>
                    <td>
                        @if ($ticket->media->count() > 0)
                            <span class="badge bg-primary status-badge">
                                <i class="fas fa-file"></i> {{ $ticket->media->count() }}
                            </span>
                        @else
                            <span class="badge bg-secondary status-badge">0</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tickets found</h5>
                            <p class="text-muted">Try adjusting your search criteria or filters</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
