{{-- Clock Details Section --}}
<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8 mt-10">

    {{-- <div class="text-center mb-8">
        <h2 class="text-xl font-bold text-gray-800">Overtime Clock Details</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Employee Name</p>
            <p class="text-gray-900 font-semibold mt-1">{{ $overtime->staff->staff_name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Overtime Date</p>
            <p class="text-gray-900 font-semibold mt-1">{{ $overtime->date->format('l, d F Y') }}</p>
        </div>
    </div> --}}

    {{-- Clock Sessions --}}
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">
        Clock Sessions
    </h3>

    <div class="space-y-4 mb-6">
        @forelse($overtime->clockSessions as $session)
            <div class="flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-lg gap-4">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs uppercase">Clock In</p>
                    <p class="text-gray-900 font-medium">
                        {{ $session->clock_in ? $session->clock_in->format('H:i') : '—' }}
                    </p>
                </div>
                <div class="flex-1">
                    <p class="text-gray-500 text-xs uppercase">Clock Out</p>
                    <p class="text-gray-900 font-medium">
                        {{ $session->clock_out ? $session->clock_out->format('H:i') : '—' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">No clock sessions recorded.</p>
        @endforelse
    </div>

    {{-- Progress Calculation --}}
    @php
        $totalRequested = $overtime->total_hours ?? 0;
        $totalRequestedSeconds = $totalRequested * 3600;

        $totalSeconds = 0;
        foreach ($overtime->clocks as $clock) {
            if ($clock->clock_in) {
                $start = \Carbon\Carbon::parse($clock->clock_in);
                $end = $clock->clock_out
                    ? \Carbon\Carbon::parse($clock->clock_out)
                    : now();

                $totalSeconds += $start->diffInSeconds($end);
            }
        }

        $totalSeconds = min($totalSeconds, $totalRequestedSeconds);
        $remainingSeconds = max(0, $totalRequestedSeconds - $totalSeconds);

        $clockedDisplay = sprintf('%02d:%02d', floor($totalSeconds / 3600), floor(($totalSeconds % 3600) / 60));
        $remainingDisplay = sprintf('%02d:%02d', floor($remainingSeconds / 3600), floor(($remainingSeconds % 3600) / 60));

        $percent = $totalRequestedSeconds > 0
            ? round(($totalSeconds / $totalRequestedSeconds) * 100)
            : 0;
    @endphp

    {{-- Remaining Hours --}}
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-blue-800 font-semibold text-sm uppercase tracking-wide">
                Remaining Overtime Hours
            </p>
            <p class="text-gray-900 font-bold text-xl mt-1">
                {{ $remainingDisplay }}
            </p>
        </div>

        <div class="w-full sm:w-1/2">
            <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-blue-600 h-4 rounded-full transition-all"
                     style="width: {{ $percent }}%">
                </div>
            </div>
            <p class="text-gray-500 text-xs mt-1 text-right">
                {{ $percent }}% Completed ({{ $clockedDisplay }})
            </p>
        </div>
    </div>

</div>
