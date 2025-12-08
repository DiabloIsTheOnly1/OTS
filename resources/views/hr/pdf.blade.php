<!DOCTYPE html>
<html>
<head>
    <title>Overtime Requests Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #130707 }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #130707; padding: 8px; text-align: left; }
        th { background-color: #f5f0f0; }
        .clock-sessions { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Overtime Requests Report - {{ now()->format('d M Y') }}</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee</th>
                <th>Branch</th>
                <th>Department</th>
                <th>Clock Sessions</th>
                <th>Requested Hours</th>
                <th>Actual Hours</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($overtimes as $r)
                <tr>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td>{{ $r->staff->staff_name ?? '-' }}</td>
                    <td>{{ $r->branch?->name ?? '-' }}</td>
                    <td>{{ $r->department?->department_name ?? '-' }}</td>
                    <td class="clock In/Out Sessions">
                        @forelse ($r->clocks as $session)
                            In: {{ $session->clock_in?->format('H:i') ?? '-' }} - 
                            Out: {{ $session->clock_out?->format('H:i') ?? '-' }} 
                            ({{ $session->total_hm }}) <br>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>{{ $r->requested_hm ?? '-' }}</td>
                    <td>{{ $r->total_hm ?? '-' }}</td>
                    <td>{{ $r->remarks ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($overtimes->isEmpty())
        <p>No requests found.</p>
    @endif
</body>
</html>