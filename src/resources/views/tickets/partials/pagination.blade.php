<!-- Cursor Pagination -->
<div class="mt-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-2 mb-md-0 text-muted">
            Showing {{ $tickets->count() }} tickets
        </div>
        <div>
            {{-- Custom cursor pagination controls --}}
            <nav aria-label="Cursor Navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($tickets->hasPages() && $tickets->previousPageUrl())
                        <li class="page-item">
                            <a class="page-link" href="{{ $tickets->previousPageUrl() }}" rel="prev">&laquo; Prev</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">&laquo; Prev</span>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($tickets->hasPages() && $tickets->nextPageUrl())
                        <li class="page-item">
                            <a class="page-link" href="{{ $tickets->nextPageUrl() }}" rel="next">
                                Next &raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next &raquo;</span>
                        </li>
                    @endif

                </ul>
            </nav>
        </div>
    </div>
</div>
