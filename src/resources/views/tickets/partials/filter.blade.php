<!-- Filter Form -->
<div class="border-bottom pb-3 mb-3">
    <form method="GET" action="{{ route('tickets.index') }}" class="row g-3 filter-form-row">
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                    In Progress</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done
                </option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="email" class="form-label">Customer Email</label>
            <input type="text" name="email" id="email" class="form-control" placeholder="Search email"
                value="{{ request('email') }}">
        </div>
        <div class="col-md-2">
            <label for="phone" class="form-label">Customer Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Search phone"
                value="{{ request('phone') }}">
        </div>
        <div class="col-md-3">
            <label for="date_from" class="form-label">Created From</label>
            <input type="date" name="date_from" id="date_from" class="form-control"
                value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">Created To</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <label for="date_answer_from" class="form-label">Answer From</label>
            <input type="date" name="date_answer_from" id="date_answer_from" class="form-control"
                value="{{ request('date_answer_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_answer_to" class="form-label">Answer To</label>
            <input type="date" name="date_answer_to" id="date_answer_to" class="form-control"
                value="{{ request('date_answer_to') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2 w-100">
                <i class="fas fa-search me-1"></i> Search
            </button>
        </div>
        <div class="col-md-12 mt-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Clear Filters
            </a>

        </div>
    </form>
</div>
