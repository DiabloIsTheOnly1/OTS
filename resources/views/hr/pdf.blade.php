<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overtime Requests Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #130707;
        }

        h1 {
            font-size: 14pt;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* prevents column stretching */
        }

        th, td {
            border: 1px solid #130707;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
            white-space: normal;
        }

        th {
            background-color: #f5f0f0;
            font-weight: bold;
            font-size: 10pt;
        }

        td {
            font-size: 9.5pt;
        }

        /* ✅ Type of Work – controlled height */
        .type-of-work {
            max-height: 2.6em; /* ~2 lines */
            overflow: hidden;
            line-height: 1.3;
        }

        /* ✅ Clock sessions – wrap nicely */
        .clock-sessions {
            white-space: normal;
            line-height: 1.4;
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
        }

        @page {
            margin: 30px;
        }
    </style>
</head>

<body>

<h1>Overtime Requests Report – {{ now()->format('d M Y') }}</h1>

@if ($overtimes->count())
<table>
    <thead>
        <tr>
            <th width="9%">Date</th>
            <th width="15%">Employee</th>
            <th width="10%">Branch</th>
            <th width="13%">Department</th>
            <th width="16%">Type of Work</th>
            <th width="20%">Clock Sessions</th>
            <th width="7%">Requested</th>
            <th width="7%">Actual</th>
            <th width="13%">Remarks</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($overtimes as $r)
        <tr>
            <td>{{ $r->date->format('d M Y') }}</td>
            <td>{{ $r->staff->staff_name ?? '-' }}</td>
            <td>{{ $r->branch?->name ?? '-' }}</td>
            <td>{{ $r->department?->department_name ?? '-' }}</td>

            <td class="type-of-work">
                {{ $r->type_of_work ?? '-' }}
            </td>

            <td class="clock-sessions">
                @forelse ($r->clocks as $session)
                    In: {{ $session->clock_in?->format('H:i') ?? '-' }}
                    – Out: {{ $session->clock_out?->format('H:i') ?? '-' }}
                    ({{ $session->total_hm }})<br>
                @empty
                    -
                @endforelse
            </td>

            <td class="text-center">{{ $r->requested_hm ?? '-' }}</td>
            <td class="text-center">{{ $r->total_hm ?? '-' }}</td>
            <td>{{ $r->remarks ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No requests found.</p>
@endif

</body>
</html>
