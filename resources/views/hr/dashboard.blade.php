@extends('layouts.app')

@section('content')

    <!-- Prevent overlap with topbar on mobile -->
    <div class="pt-[65px] sm:pt-6 lg:pt-4 transition-all">

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-700 tracking-tight">
                    Overtime Requests
                </h1>
                <p class="text-sm text-gray-500 mt-1 hidden sm:block">
                    {{ now()->format('l, d F Y') }}
                </p>
                <p class="text-sm text-gray-500 mt-1 sm:hidden">
                    {{ now()->format('D, d M Y') }}
                </p>
            </div>

            @canAccess('manage_request')
                <a href="{{ route('overtime.create') }}"
                   class="inline-flex items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 px-4 py-2 font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 group">

                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden sm:inline">New Overtime Request</span>
                        <span class="sm:hidden">New Request</span>
                    </span>

                    <span class="absolute inset-0 -translate-x-full bg-white/20 skew-x-12 transition-transform duration-700 group-hover:translate-x-full"></span>
                </a>
            @endcanAccess
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
            <form method="GET" id="filter-form" action="{{ route('hr.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end">

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Branch</label>
                    <select name="branch_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Department</label>
                    <select name="department_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Employee</label>
                    <input type="text" name="name" value="{{ request('name') }}"
                           class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- From -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">From</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                           class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- To -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">To</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                           class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Status</label>
                    <select name="status" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-span-1 sm:col-span-2 md:col-span-6 flex items-center gap-2">
                    <button type="submit"
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition">
                        Apply Filter
                    </button>

                    <button type="button" id="open-report-modal"
                            class="relative inline-flex items-center justify-center overflow-hidden rounded-md bg-gradient-to-br from-indigo-600 to-purple-600 px-4 py-1.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:shadow-lg hover:from-indigo-700 hover:to-purple-700 group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generate Report
                        </span>
                        <span class="absolute inset-0 -translate-x-full bg-white/20 skew-x-12 transition-transform duration-700 group-hover:translate-x-full"></span>
                    </button>

                    <a href="{{ route('hr.dashboard') }}"
                       class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-medium text-sm transition">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>

        {{-- Responsive table wrapper --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[300px] text-sm">
                    <thead class="bg-blue-100 text-gray-800 border-b">
                        <tr class="hidden md:table-row">
                            <th class="p-3 text-left font-semibold whitespace-nowrap">Date</th>
                            <th class="p-3 text-left font-semibold">Employee</th>
                            <th class="p-3 font-semibold">Clock in/Out</th>
                            <th class="p-3 font-semibold text-center whitespace-nowrap">Requested Hours</th>
                            <th class="p-3 font-semibold text-center whitespace-nowrap">Actual Hours</th>
                            <th class="p-3 font-semibold text-center whitespace-nowrap">Status</th>
                            <th class="p-3 font-semibold text-center whitespace-nowrap">Approval</th>
                            <th class="p-3 text-left font-semibold">Remarks</th>
                            <th class="p-3 font-semibold text-center whitespace-nowrap">Action</th>
                        </tr>
                        <tr class="table-row md:hidden">
                            <th>Request</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $r)
                            @php
                                $bg = match ($r->status) {
                                    'pending' => 'bg-yellow-50',
                                    'approved' => 'bg-green-50',
                                    'rejected' => 'bg-red-50',
                                    default => '',
                                };
                            @endphp

                            {{-- DESKTOP ROW --}}
                            <tr class="{{ $bg }} border-b hover:bg-blue-50 transition hidden md:table-row">
                                <td class="p-3 whitespace-nowrap">{{ $r->date->format('d M Y') }}</td>

                                <td class="p-3">
                                    <p class="font-semibold">{{ $r->staff->staff_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $r->branch?->name ?? '-' }} • {{ $r->department?->department_name ?? '-' }}</p>
                                </td>

                                <!-- Clock In/Out -->
                                <td class="p-3">
                                    <div class="space-y-1">
                                        @forelse ($r->clocks as $session)
                                            <div class="px-2 py-1 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="flex items-center justify-between text-sm">
                                                    <div>
                                                        <span class="text-gray-600">In:</span>
                                                        {{ $session->clock_in?->format('H:i') ?? '-' }} -
                                                        <span class="text-gray-600">Out:</span>
                                                        {{ $session->clock_out?->format('H:i') ?? '-' }}
                                                    </div>
                                                    <span class="text-blue-600 font-bold text-xs bg-blue-50 px-2 py-0.5 rounded">
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
                                    <span class="inline-block bg-amber-100 text-amber-800 font-bold px-3 py-1 rounded-full text-sm">
                                        {{ $r->requested_hm ?? '-' }}
                                    </span>
                                </td>

                                <!-- Actual Hours -->
                                <td class="p-3 font-bold text-blue-700 text-center">{{ $r->total_hm }}</td>

                                <!-- Status -->
                                <td class="text-center p-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if ($r->status == 'pending') bg-yellow-200 text-yellow-900
                                        @elseif($r->status == 'approved') bg-green-200 text-green-900
                                        @else bg-red-200 text-red-900 @endif">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>

                                <!-- Approval -->
                                <td class="p-3 text-center">
                                    @if ($r->status === 'pending')
                                        @php
                                            $canHod = auth()->user()->canAccess('hod_approval');
                                            $canHq = auth()->user()->canAccess('hq_approval');

                                            $createdAt = \Carbon\Carbon::parse($r->created_at);
                                            $deadline = $createdAt->copy()->addHours(24);
                                            $now = \Carbon\Carbon::now();

                                            $remainingSeconds = max(0, $deadline->diffInSeconds($now));
                                            $hoursSinceCreated = $createdAt->diffInHours($now);

                                            $hodWindow = $hoursSinceCreated <= 24;
                                            $hqWindow = $hoursSinceCreated > 24;

                                            $buttonEnabled = ($hodWindow && $canHod) || ($hqWindow && $canHq);
                                        @endphp

                                        <div x-data="{
                                            seconds: {{ $remainingSeconds }},
                                            expired: {{ $hqWindow || $remainingSeconds <= 0 ? 'true' : 'false' }}
                                        }" x-init="if (seconds > 0) setInterval(() => { if (seconds > 0) seconds--; if (seconds <= 0) expired = true; }, 1000);"
                                             class="flex flex-col items-center gap-2">

                                            <div class="flex gap-2 justify-center mt-1">
                                                <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST"
                                                      onsubmit="return confirm('Approve this request?');">
                                                    @csrf
                                                    <button x-bind:disabled="expired || {{ !$buttonEnabled }}"
                                                            class="px-3 py-1 text-xs rounded {{ $buttonEnabled ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST"
                                                      onsubmit="return confirm('Reject this request?');">
                                                    @csrf
                                                    <button x-bind:disabled="expired || {{ !$buttonEnabled }}"
                                                            class="px-3 py-1 text-xs rounded {{ $buttonEnabled ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>

                                            <p class="text-xs text-red-600 mt-1" x-show="expired">
                                                HQ approval required
                                            </p>
                                        </div>
                                    @else
                                        <p class="text-xs">{{ $r->status == 'approved' ? 'Approved' : 'Rejected' }} by</p>
                                        <span class="font-bold text-gray-800 text-xs">{{ $r->approver?->username ?? '-' }}</span>
                                    @endif
                                </td>

                                <!-- Remarks -->
                                <td class="p-3">
                                    <div class="group">
                                        <div class="flex items-center gap-2 remark-display">
                                            <span>{{ $r->remarks ?: '-' }}</span>
                                            <button type="button" class="hidden group-hover:inline text-blue-600 text-xs remark-edit-btn">Edit</button>
                                        </div>
                                        <form action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                                              class="hidden remark-edit-form mt-1 flex gap-1">
                                            @csrf
                                            <input name="remarks" value="{{ $r->remarks }}" class="border px-2 py-1 rounded text-xs w-28">
                                            <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Save</button>
                                            <button type="button" class="remark-cancel-btn text-xs text-gray-500 px-1">Cancel</button>
                                        </form>
                                    </div>
                                </td>

                                <!-- Action -->
                                <td class="p-3 text-center space-x-1">
                                    <a href="{{ route('overtime.success', $r->id) }}"
                                       class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-xs inline-flex items-center">
                                        QR
                                    </a>
                                    <a href="{{ route('hr.overtime.view', $r->id) }}"
                                       class="px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs inline-flex items-center"
                                       title="View Overtime Request">
                                        View
                                    </a>
                                </td>
                            </tr>

                            {{-- MOBILE CARD ROW --}}
                            <tr class="table-row md:hidden">
                                <td colspan="10">
                                    <div class="m-3 border rounded-lg bg-white p-4 shadow-sm space-y-3">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $r->staff->staff_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $r->branch?->name ?? '-' }} • {{ $r->department?->department_name ?? '-' }}</p>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-700">{{ $r->date->format('d M Y') }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                                @if ($r->status == 'pending') bg-yellow-200 text-yellow-900
                                                @elseif($r->status == 'approved') bg-green-200 text-green-900
                                                @else bg-red-200 text-red-900 @endif">
                                                {{ ucfirst($r->status) }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Clock Sessions</p>
                                            <div class="space-y-2">
                                                @forelse ($r->clocks as $session)
                                                    <div class="bg-gray-50 border rounded px-3 py-2 text-xs flex justify-between">
                                                        <div>
                                                            <span class="text-gray-600">In:</span> {{ $session->clock_in?->format('H:i') ?? '-' }} <br>
                                                            <span class="text-gray-600">Out:</span> {{ $session->clock_out?->format('H:i') ?? '-' }}
                                                        </div>
                                                        <p class="text-blue-700 font-bold">{{ $session->total_hm }}</p>
                                                    </div>
                                                @empty
                                                    <span class="text-gray-400">-</span>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 font-medium">Requested Hours:</span>
                                            <span class="font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                                                {{ $r->requested_hm ?? '-' }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between font-bold text-sm">
                                            <span>Actual Hours:</span>
                                            <span class="text-blue-700">{{ $r->total_hm }}</span>
                                        </div>

                                        <div class="text-center">
                                            <a href="{{ route('hr.overtime.view', $r->id) }}"
                                               class="inline-flex items-center justify-center bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700 w-full">
                                                View Form
                                            </a>
                                        </div>

                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-500">Remarks</p>
                                            <p class="font-bold text-gray-800 text-xs">{{ $r->remarks ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center p-8 text-gray-500">No requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- JS for remark edit toggle --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".remark-edit-btn").forEach(btn => {
                    const parent = btn.closest(".group");
                    const display = parent.querySelector(".remark-display");
                    const form = parent.querySelector(".remark-edit-form");
                    const cancel = parent.querySelector(".remark-cancel-btn");

                    btn.addEventListener("click", () => {
                        display.classList.add("hidden");
                        form.classList.remove("hidden");
                    });

                    cancel?.addEventListener("click", () => {
                        form.classList.add("hidden");
                        display.classList.remove("hidden");
                    });
                });
            });
        </script>

        {{-- REPORT PREVIEW MODAL --}}
        <div id="report-modal" class="fixed inset-0 bg-black bg-opacity-60 z-[9999] hidden flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full h-[90vh] flex flex-col overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-5 flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Overtime Report Preview</h2>
                    <button onclick="closeReportModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 bg-gray-50">
                    <iframe id="report-iframe" class="w-full h-full border-0" src="" frameborder="0"></iframe>
                </div>
                <div class="bg-white border-t p-5 flex flex-wrap gap-3 justify-end">
                    <button onclick="printReport()" class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        Print
                    </button>
                    <button onclick="downloadExcel()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        Excel
                    </button>
                    <button onclick="downloadPDF()" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- REPORT MODAL SCRIPT --}}
        <script>
            const modal = document.getElementById('report-modal');
            const iframe = document.getElementById('report-iframe');

            document.getElementById('open-report-modal').addEventListener('click', () => {
                const params = new URLSearchParams(window.location.search);
                iframe.src = '{{ route("hr.overtime.preview") }}?' + params.toString();
                modal.classList.remove('hidden');
            });

            function closeReportModal() {
                modal.classList.add('hidden');
                iframe.src = '';
            }

            function downloadExcel() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route("hr.overtime.export.excel") }}?' + params.toString();
            }

            function downloadPDF() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route("hr.overtime.export.pdf") }}?' + params.toString();
            }

            function printReport() {
                iframe.contentWindow.print();
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeReportModal();
            });
        </script>
    </div>
@endsection