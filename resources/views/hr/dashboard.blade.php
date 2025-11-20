<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Overtime Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('.layout.topbar')

    <div class="container my-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">HR Dashboard - Overtime Requests</h1>

            <div>
                <a href="{{ route('overtime.form') }}" class="btn btn-success me-2">
                    + Create Request
                </a>
            </div>
        </div>

        {{-- Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by employee name"
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Hours</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Approved By</th>

                            @auth
                                <th>Actions</th>
                            @endauth
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $r)
                            <tr
                                class="{{ $r->status == 'pending' ? 'table-warning' : ($r->status == 'approved' ? 'table-success' : 'table-danger') }}">
                                <td>{{ $r->date }}</td>
                                <td>{{ $r->employee_name }}</td>
                                <td>{{ $r->department }}</td>
                                <td>{{ $r->start_time }}</td>
                                <td>{{ $r->end_time }}</td>
                                <td>{{ $r->total_hours }}</td>
                                <td>{{ $r->reason }}</td>
                                <td>{{ ucfirst($r->status) }}</td>
                                <td>{{ $r->approver?->name ?? '-' }}</td>

                                @auth
                                    <td>
                                        @if ($r->status == 'pending')
                                            <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>

                                            <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        @else
                                            <span class="text-muted">No actions</span>
                                        @endif
                                    </td>
                                @endauth
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::check() ? '10' : '9' }}" class="text-center">No overtime requests
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
