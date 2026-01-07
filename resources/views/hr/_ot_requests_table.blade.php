{{-- Responsive table wrapper --}}
<div class="bg-white shadow-xl rounded-xl overflow-hidden">
    <div class="w-full overflow-x-auto">
        {{-- <table class="w-full min-w-[300px] text-sm"> --}}
        <table class="w-full text-sm table-fixed">
            <thead class="bg-blue-100 text-gray-800 border-b">
                <tr class="hidden md:table-row">
                    <th class="p-3 w-[110px] text-left font-semibold">Date</th>
                    <th class="p-3 w-[220px] text-left font-semibold">Employee</th>
                    <th class="p-3 w-[260px] font-semibold">Clock in/Out</th>
                    <th class="p-3 w-[140px] font-semibold text-center">Requested Hours</th>
                    <th class="p-3 w-[140px] font-semibold text-center">Actual Hours</th>
                    <th class="p-3 w-[140px] font-semibold text-center">Type of Work</th>
                    <th class="p-3 w-[120px] font-semibold text-center">Status</th>
                    <th class="p-3 w-[260px] font-semibold text-center">Approval</th>
                    <th class="p-3 w-[220px] text-left font-semibold">Remarks</th>
                    <th class="p-3 w-[120px] font-semibold text-center">Action</th>
                </tr>
                <tr class="table-row md:hidden">
                    <th>Request</th>
                </tr>
            </thead>

            <tbody>
                @forelse($requests as $r)
                    @php
                        $isDeleted = $r->staff?->trashed();

                        $bg = match ($r->status) {
                            'pending' => 'bg-yellow-50',
                            'approved' => 'bg-green-50',
                            'rejected' => 'bg-red-50',
                            default => '',
                        };
                    @endphp

                    {{-- DESKTOP ROW --}}
                    <tr
                        class="{{ $isDeleted ? 'opacity-60 bg-gray-50' : $bg }} border-b hover:bg-blue-50 transition hidden md:table-row">

                        <td class="p-3 whitespace-nowrap">
                            {{ $r->date->format('d M Y') }}
                            {{-- <p class="text-xs text-gray-500">{{ $r->created_at->format('h:i A') }}</p> --}}
                        </td>

                        <td class="p-3">
                            {{-- @php
                                        $isDeleted = $r->staff?->trashed();
                                    @endphp --}}

                            <p
                                class="font-semibold
                                        {{ $isDeleted ? 'text-gray-400 line-through italic' : '' }}">
                                {{ $r->staff->staff_name ?? '-' }}
                            </p>

                            <p
                                class="text-xs
                                        {{ $isDeleted ? 'text-gray-400 line-through' : 'text-gray-500' }}">
                                {{ $r->branch?->name ?? '-' }} •
                                {{ $r->department?->department_name ?? '-' }}
                            </p>
                        </td>


                        <!-- Clock In/Out -->
                        <td class="p-3">
                            <div class="space-y-1">
                                @forelse ($r->clocks as $session)
                                    <div class="px-2 py-1 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="lg:flex-col">
                                                <span class="text-gray-600">In:</span>
                                                {{ $session->clock_in?->format('H:i') ?? '-' }} -
                                                <span class="text-gray-600">Out:</span>
                                                {{ $session->clock_out?->format('H:i') ?? '-' }}
                                                @if ($session->auto_flag)
                                                    <span class="text-xs italic text-orange-500">Auto</span>
                                                @endif
                                            </div>
                                            <span
                                                class="text-blue-600 font-bold text-xs bg-blue-50 px-2 py-0.5 rounded">
                                                {{ $session->total_hm }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Requested -->
                        <td class="p-3 text-center">
                            <span
                                class="inline-block bg-amber-100 text-amber-800 font-bold px-3 py-1 rounded-full text-sm">
                                {{ $r->requested_hm ?? '-' }}
                            </span>
                        </td>

                        @php
                            // APPROVED HOURS (if approved)
                            if ($r->approved_hours !== null) {
                                $apprHours = floor($r->approved_hours);
                                $apprMinutes = round(($r->approved_hours - $apprHours) * 60);
                                $r->approved_hm = sprintf('%02d:%02d', $apprHours, $apprMinutes);
                            }
                        @endphp
                        <!-- Total -->
                        <td class="p-3 text-center">

                            {{-- Always show actual --}}
                            <div class="font-bold text-blue-700 text-sm">
                                {{ $r->actual_hm }}
                            </div>

                            @if ($r->status === 'approved')
                                @if ($r->approved_hm)
                                    <div class="text-purple-700 text-xs font-semibold">
                                        Approved: {{ $r->approved_hm }}
                                        {{-- <span class="text-purple-500 font-bold">(Partial)</span> --}}
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- Type of Work --}}
                        <td class="p-3 text-center">
                            <span class="text-gray-700 text-sm  whitespace-pre-line break-words">
                                {{ $r->type_of_work }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="text-center p-3">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($r->status == 'pending') bg-yellow-200 text-yellow-900
                                            @elseif($r->status == 'approved') bg-green-200 text-green-900
                                            @else bg-red-200 text-red-900 @endif">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>

                        <!-- Approval -->
                        <td class="px-3 py-1 text-center">
                            @if ($isDeleted)
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="px-3 py-1 text-xs rounded bg-gray-200 text-gray-400 cursor-not-allowed"
                                        disabled>
                                        Approve
                                    </button>
                                    <button
                                        class="px-3 py-1 text-xs rounded bg-gray-200 text-gray-400 cursor-not-allowed"
                                        disabled>
                                        Partial
                                    </button>
                                    <button
                                        class="px-3 py-1 text-xs rounded bg-gray-200 text-gray-400 cursor-not-allowed"
                                        disabled>
                                        Reject
                                    </button>
                                </div>

                                <p class="text-xs text-gray-400 mt-1 italic">
                                    Actions disabled (staff deleted)
                                </p>
                            @else
                                @if ($r->status === 'pending')
                                    @php
                                        $canHod = auth()->user()->canAccess('hod_approval');
                                        $canHq = auth()->user()->canAccess('hq_approval');

                                        $firstClockIn = optional($r->clocks->sortBy('clock_in')->first())->clock_in;

                                        $now = \Carbon\Carbon::now();

                                        if ($firstClockIn) {
                                            $deadline = $firstClockIn->copy()->addHours(48);
                                            $hoursSinceFirstClockIn = $firstClockIn->diffInHours($now);
                                            $remainingSeconds = max(0, $deadline->diffInSeconds($now));
                                        } else {
                                            // fallback safety (no clock yet)
                                            $hoursSinceFirstClockIn = 0;
                                            $remainingSeconds = 0;
                                        }

                                        $hodWindow = $hoursSinceFirstClockIn <= 48;
                                        $hqWindow = $hoursSinceFirstClockIn > 48;

                                        $canApprove = ($hodWindow && $canHod) || ($hqWindow && $canHq);
                                    @endphp

                                    <!-- Alpine Modal -->
                                    <div x-data="{
                                        seconds: {{ $remainingSeconds }},
                                        openPartial: false,
                                        hm: '{{ $r->actual_hm }}',
                                        toMinutes(hm) {
                                            let [h, m] = hm.split(':').map(Number);
                                            return (h * 60) + m;
                                        }
                                    }" x-init="setInterval(() => { if (seconds > 0) seconds-- }, 1000)">
                                        <!-- Approve + Reject Buttons -->
                                        <div class="flex gap-2 justify-center">

                                            {{-- APPROVE FULL BUTTON --}}
                                            <form action="{{ route('hr.overtime.approveFull', $r->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Approve full actual overtime?');">
                                                @csrf
                                                <button
                                                    class="px-3 py-1 text-xs rounded
                                                            @if ($canApprove) bg-green-600 hover:bg-green-700 text-white
                                                            @elseif (!$canHod && !$canHq) bg-gray-300 text-gray-500 cursor-not-allowed
                                                            @else bg-gray-800 text-gray-400 cursor-not-allowed @endif">
                                                    Approve
                                                </button>
                                            </form>

                                            {{-- PARTIAL APPROVAL BUTTON --}}
                                            <x-partial-approve :id="$r->id" :actualHm="$r->actual_hm" :actualMinutes="$r->actual_minutes"
                                                :requestedHm="$r->requested_hm" :requestedMinutes="$r->requested_minutes" :canApprove="$canApprove" :canHod="$canHod"
                                                :canHq="$canHq" />

                                            {{-- Reject --}}
                                            <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST"
                                                onsubmit="return confirm('Reject this request?');">
                                                @csrf
                                                <button
                                                    class="px-3 py-1 text-xs rounded
                                                            @if ($canApprove) bg-red-600 hover:bg-red-700 text-white
                                                            @elseif (!$canHod && !$canHq) bg-gray-300 text-gray-500 cursor-not-allowed
                                                            @else bg-gray-800 text-gray-400 cursor-not-allowed @endif">
                                                    Reject
                                                </button>
                                            </form>

                                        </div>

                                        <!-- HQ notice -->
                                        <p x-show="{{ $hqWindow ? 'true' : 'false' }}"
                                            class="text-xs text-red-600 mt-1">
                                            HQ approval required
                                        </p>
                                    </div>
                                @else
                                    <p class="text-xs">{{ $r->status == 'approved' ? 'Approved' : 'Rejected' }} by
                                    </p>
                                    <span
                                        class="font-bold text-gray-800 text-xs">{{ $r->approver?->username ?? '-' }}</span>
                                    <p class="italic text-gray-800 text-xs">{{ $r->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                                @endif
                            @endif
                        </td>

                        <!-- Remarks -->
                        <td class="p-3 max-w-52">
                            <div class="group">

                                {{-- Display Mode --}}
                                <div class="remark-display">
                                    <p class="text-gray-700 text-sm whitespace-pre-line break-words">
                                        {{ $r->remarks ?: '-' }}
                                    </p>

                                    @if (!$isDeleted)
                                        <button type="button"
                                            class="hidden group-hover:inline text-blue-600 text-xs remark-edit-btn mt-1">✏️
                                        </button>
                                    @endif
                                </div>

                                {{-- Edit Mode --}}
                                <form @if ($isDeleted) onsubmit="return false;" @endif
                                    action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                                    class="hidden remark-edit-form mt-2 flex flex-col gap-2">@csrf

                                    <textarea name="remarks" rows="3" class="border w-full px-2 py-1 rounded text-xs break-words">{{ $r->remarks }}</textarea>

                                    <div class="flex gap-2">
                                        <button
                                            class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Save</button>
                                        <button type="button"
                                            class="remark-cancel-btn text-xs text-gray-500 px-2">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </td>

                        {{-- Action  --}}
                        <td class="p-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ $isDeleted ? '#' : route('overtime.success', $r->id) }}"
                                class="px-2 py-1 rounded text-xs inline-flex items-center whitespace-nowrap
                                    {{ $isDeleted
                                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none'
                                            : 'bg-blue-100 text-blue-600 hover:bg-blue-200' }}">
                                <i class="fas fa-qrcode mr-1"></i> QR
                            </a>

                            <a href="{{ $isDeleted ? '#' : route('hr.overtime.view', $r->id) }}"
                                class="px-2 py-1 rounded text-xs inline-flex items-center whitespace-nowrap
                                    {{ $isDeleted
                                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <i class="fa-solid fa-eye mr-1"></i> View
                            </a>
                        </div>
                    </td>


                    </tr>

                    {{-- MOBILE CARD ROW --}}
                    @include('hr._mobile_row', ['r' => $r])

                @empty
                    <tr>
                        <td colspan="10" class="text-center p-4 text-gray-500">No requests found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
