<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overtime Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: white; color: #333; }
        h1 { text-align: center; color: #4f46e5; }
        .info { text-align: center; margin-bottom: 30px; font-size: 14px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4f46e5; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px 12px; border-bottom: 1px solid #ddd; vertical-align: top; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .clock { font-size: 13px; line-height: 1.4; }
        .status-pending { background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .status-approved { background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .status-rejected { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .no-data { text-align: center; padding: 50px; color: #888; font-size: 16px; }
    </style>
</head>
<body>

    <h1>Overtime Requests Report</h1>
    <div class="info">
        Generated: {{ now()->format('d M Y H:i') }}<br>
        @if(collect(request()->except('page'))->isNotEmpty())
    <span class="text-green-700 font-medium">Filters Applied: Yes</span>
    @else
    <span class="text-gray-500">No filters applied</span>
    @endif
    </div>

    @if($overtimes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="10%">Date</th>
                    <th width="18%">Employee</th>
                    <th width="10%">Branch</th>
                    <th width="14%">Department</th>
                    <th width="22%">Clock Sessions</th>
                    <th width="9%">Requested</th>
                    <th width="9%">Actual</th>
                    <th width="8%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overtimes as $r)
                    <tr>
                        <td><strong>{{ $r->date->format('d M Y') }}</strong></td>
                        <td><strong>{{ $r->staff->staff_name ?? 'N/A' }}</strong></td>
                        <td>{{ $r->branch?->name ?? 'N/A' }}</td>
                        <td>{{ $r->department?->department_name ?? 'N/A' }}</td>
                        <td class="clock">
                            @forelse($r->clocks as $c)
                                In: {{ $c->clock_in?->format('H:i') ?? '-' }} → 
                                Out: {{ $c->clock_out?->format('H:i') ?? '-' }}
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
        <div class="no-data">No overtime requests found matching your filters.</div>
    @endif

</body>
</html>