{{-- MOBILE CARD ROW --}}
<tr class="table-row md:hidden">
    <td colspan="10">
        <div class="m-3 border rounded-lg bg-white p-4 shadow-sm space-y-3">

            {{-- Employee Info --}}
            <div>
                <p class="font-bold text-gray-900">
                    {{ $r->staff->staff_name ?? '-' }}</p>
                <p class="text-xs text-gray-500">
                    {{ $r->branch?->name ?? '-' }} •
                    {{ $r->department?->department_name ?? '-' }}</p>
            </div>

            {{-- Date & Status --}}
            <div class="flex justify-between items-center">
                <span class="text-xs font-medium text-gray-700">
                    {{ $r->date->format('d M Y') }}
                </span>
                <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    @if ($r->status == 'pending') bg-yellow-200 text-yellow-900
                    @elseif($r->status == 'approved') bg-green-200 text-green-900
                    @else bg-red-200 text-red-900 @endif">
                    {{ ucfirst($r->status) }}
                </span>
            </div>

            {{-- Clock Sessions --}}
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Clock Sessions</p>
                <div class="space-y-2">
                    @forelse ($r->clocks as $session)
                        <div class="bg-gray-50 border rounded px-3 py-2 text-xs flex justify-between">
                            <div>
                                <span class="text-gray-600">In:</span>
                                {{ $session->clock_in?->format('H:i') ?? '-' }}
                                <br>
                                <span class="text-gray-600">Out:</span>
                                {{ $session->clock_out?->format('H:i') ?? '-' }}
                            </div>
                            <p class="text-blue-700 font-bold">{{ $session->total_hm }}</p>
                        </div>
                    @empty
                        <span class="text-gray-400">-</span>
                    @endforelse
                </div>
            </div>

            {{-- Requested Hours --}}
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 font-medium">Requested Hours:</span>
                <span class="font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                    {{ $r->requested_hm ?? '-' }}
                </span>
            </div>

            {{-- Actual Hours --}}
            <div class="flex justify-between font-bold text-sm">
                <span>Actual Hours:</span>
                <span class="text-blue-700">{{ $r->actual_hm }}</span>
            </div>

            {{-- Type of Work --}}
            <div class="rounded-lg bg-gray-50 p-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400 mb-1">
                Type of Work
            </p>
            <p class="text-sm text-gray-700 leading-relaxed break-words whitespace-pre-line">
                {{ $r->type_of_work }}
            </p>
            </div>

            

            {{-- Approval Buttons --}}
            <div class="flex flex-col gap-2 mt-3">
                @if ($r->status === 'pending')
                    @php
                        $canHod = auth()->user()->canAccess('hod_approval');
                        $canHq = auth()->user()->canAccess('hq_approval');
                        $createdAt = \Carbon\Carbon::parse($r->created_at);
                        $hoursSinceCreated = $createdAt->diffInHours(now());
                        $hodWindow = $hoursSinceCreated <= 48;
                        $hqWindow = $hoursSinceCreated > 48;
                        $canApprove = ($hodWindow && $canHod) || ($hqWindow && $canHq);
                    @endphp

                    {{-- Approve Full --}}
                    <form action="{{ route('hr.overtime.approveFull', $r->id) }}" method="POST"
                        onsubmit="return confirm('Approve full actual overtime?');">
                        @csrf
                        <button
                            class="px-3 py-1 text-xs rounded w-full
                            @if ($canApprove) bg-green-600 hover:bg-green-700 text-white
                            @else bg-gray-300 text-gray-500 cursor-not-allowed @endif">
                            Approve
                        </button>
                    </form>

                    {{-- Partial --}}
                    <x-partial-approve :id="$r->id" :actualHm="$r->actual_hm" :actualMinutes="$r->actual_minutes" :requestedHm="$r->requested_hm"
                        :requestedMinutes="$r->requested_minutes" :canApprove="$canApprove" :canHod="$canHod" :canHq="$canHq" />

                    {{-- Reject --}}
                    <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST"
                        onsubmit="return confirm('Reject this request?');">
                        @csrf
                        <button
                            class="px-3 py-1 text-xs rounded w-full
                            @if ($canApprove) bg-red-600 hover:bg-red-700 text-white
                            @else bg-gray-300 text-gray-500 cursor-not-allowed @endif">
                            Reject
                        </button>
                    </form>
                @else
                    <div class="flex justify-between text-sm">
                        <p class="text-xs">{{ $r->status == 'approved' ? 'Approved' : 'Rejected' }} by</p>
                        <span class="font-bold text-gray-800 text-sm">
                            {{ $r->approver?->username ?? '-' }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Remarks --}}
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Remarks</p>

                <div class="group">
                    {{-- Display --}}
                    <div class="remark-display flex justify-between items-start">
                        <p class="text-gray-700 text-sm whitespace-pre-line break-words break-all leading-relaxed">
                            {{ $r->remarks ?: '-' }}
                        </p>
                        <button type="button" class="text-blue-600 text-xs remark-edit-btn mt-1">✏️</button>
                    </div>

                    {{-- Edit --}}
                    <form action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                        class="hidden remark-edit-form mt-2 flex flex-col gap-2">
                        @csrf
                        <textarea name="remarks" rows="3" class="border w-full px-2 py-1 rounded text-xs break-words">{{ $r->remarks }}</textarea>

                        <div class="flex gap-2">
                            <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                Save
                            </button>
                            <button type="button" class="remark-cancel-btn text-xs text-gray-500 px-2">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 gap-3 mt-3">
                <a href="{{ route('overtime.success', $r->id) }}"
                    class="inline-flex items-center justify-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-indigo-700">
                    <i class="fas fa-qrcode mr-1"></i> QR
                </a>
                <a href="{{ route('hr.overtime.view', $r->id) }}"
                    class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    View Form
                </a>
            </div>

        </div>
    </td>
</tr>
