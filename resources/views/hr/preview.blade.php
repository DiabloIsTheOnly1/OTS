<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overtime Report</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
        background: #ffffff;
        color: #333;
        font-size: 13px;
    }

    h1 {
        text-align: center;
        color: #4f46e5;
        margin-bottom: 10px;
        font-size: 20px;
    }

    .info {
        text-align: center;
        margin-bottom: 25px;
        font-size: 13px;
        color: #555;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* prevents column stretching */
    }

    th {
        background: #4f46e5;
        color: #ffffff;
        padding: 10px;
        text-align: left;
        font-weight: bold;
        font-size: 12px;
        border-bottom: 2px solid #4338ca;
    }

    td {
        padding: 9px 10px;
        border-bottom: 1px solid #ddd;
        vertical-align: top;
        font-size: 12px;

        word-break: break-word;
        white-space: normal;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* ✅ FIXED: Type of Work (PDF safe 2-line clamp) */
    .type-of-work {
        color: #555;
        line-height: 1.4;
        max-height: 2.8em;     /* approx 2 lines */
        overflow: hidden;
    }

    /* ✅ FIXED: Clock column wrapping */
    .clock {
        line-height: 1.4;
        white-space: normal;
    }

    .text-center {
        text-align: center;
    }

    .no-data {
        text-align: center;
        padding: 50px;
        color: #888;
        font-size: 15px;
    }

    /* Optional: improve PDF readability */
    @page {
        margin: 40px;
    }
</style>

</head>
<body>

<h1>Overtime Requests Report</h1>

<div class="info">
    Generated: {{ now()->format('d M Y H:i') }}<br>
    @if(collect(request()->except('page'))->isNotEmpty())
        <strong style="color:#15803d">Filters Applied</strong>
    @else
        <span style="color:#6b7280">No filters applied</span>
    @endif
</div>

@if($overtimes->count() > 0)
<table>
    <thead>
        <tr>
            <th width="9%">Date</th>
            <th width="16%">Employee</th>
            <th width="10%">Branch</th>
            <th width="12%">Department</th>
            <th width="16%">Type of Work</th>
            <th width="22%">Clock Sessions</th>
            <th width="7%">Requested</th>
            <th width="7%">Actual</th>
            <th width="9%">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($overtimes as $r)
        <tr>
            <td><strong>{{ $r->date->format('d M Y') }}</strong></td>
            <td><strong>{{ $r->staff->staff_name ?? 'N/A' }}</strong></td>
            <td>{{ $r->branch?->name ?? 'N/A' }}</td>
            <td>{{ $r->department?->department_name ?? 'N/A' }}</td>

           
            <td class="type-of-work">
                {{ $r->type_of_work ?? '-' }}
            </td>

            <td class="clock">
                @forelse($r->clocks as $c)
                    In: {{ $c->clock_in?->format('H:i') ?? '-' }}
                    → Out: {{ $c->clock_out?->format('H:i') ?? '-' }}
                    <strong>({{ $c->total_hm }})</strong><br>
                @empty
                    <span style="color:#999">-</span>
                @endforelse
            </td>

            <td class="text-center"><strong>{{ $r->requested_hm ?? '-' }}</strong></td>
            <td class="text-center"><strong>{{ $r->total_hm ?? '-' }}</strong></td>
            <td>{{ $r->remarks ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">
    No overtime requests found matching your filters.
</div>
@endif

</body>
</html>
