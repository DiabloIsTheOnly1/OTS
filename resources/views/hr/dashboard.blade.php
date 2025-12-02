@extends('layouts.app')

@section('content')

    {{-- Title + Date responsive --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-blue-700 tracking-tight">
            Overtime Requests
        </h1>
        <div class="text-sm text-gray-500 mt-1 block md:hidden">
            {{ now()->format('l, d M Y') }}
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end">

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
            <div class="flex gap-2 col-span-1 sm:col-span-2 md:col-span-1">
                <button type="submit"
                    class="px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg w-full md:w-auto transition">
                    Filter
                </button>

                <a href="{{ route('hr.dashboard') }}"
                    class="px-4 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg w-full md:w-auto transition text-center">
                    Reset
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
                        <th class="p-3 font-semibold whitespace-nowrap">Date</th>
                        <th class="p-3 font-semibold">Employee</th>
                        <th class="p-3 font-semibold">Clock in/Out</th>
                        <th class="p-3 font-semibold text-center whitespace-nowrap">Total Hours</th>
                        <th class="p-3 font-semibold">Reason</th>
                        <th class="p-3 font-semibold text-center whitespace-nowrap">Status</th>
                        <th class="p-3 font-semibold text-center whitespace-nowrap">Approval</th>
                        <th class="p-3 font-semibold">Remarks</th>
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

                        {{-- Desktop row --}}
                        <tr class="{{ $bg }} border-b hover:bg-blue-50 transition hidden md:table-row">

                            <td class="p-3 whitespace-nowrap">{{ $r->date->format('d M Y') }}</td>

                            <td class="p-3">
                                <p class="font-semibold">{{ $r->name }}</p>
                                <p class="text-xs text-gray-500">{{ $r->branch?->name ?? '-' }} •
                                    {{ $r->department?->department_name ?? '-' }}</p>
                            </td>

                            <td class="p-3">
                                <div class="space-y-1">
                                    @forelse ($r->clocks as $session)
                                        <div class="px-2 py-0.5 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center justify-between text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center space-x-1">
                                                        <span class="font-medium text-gray-600">In:</span>
                                                        <span
                                                            class="text-gray-900">{{ $session->clock_in ? $session->clock_in->format('H:i') : '-' }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1">
                                                        <span class="font-medium text-gray-600">Out:</span>
                                                        <span
                                                            class="text-gray-900">{{ $session->clock_out ? $session->clock_out->format('H:i') : '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="font-medium text-blue-600 text-xs bg-blue-50 px-2 py-1 rounded">
                                                    {{ $session->total_hm }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="p-3 font-bold text-blue-700 text-center">{{ $r->total_hm }}</td>
                            <td class="p-3">{{ $r->reason ?? '-' }}</td>

                            <td class="text-center p-3">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                @if ($r->status == 'pending') bg-yellow-200 text-yellow-900
                                @elseif($r->status == 'approved') bg-green-200 text-green-900
                                @else bg-red-200 text-red-900 @endif">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>

                            <td class="p-3 text-center">
                                @if ($r->status === 'pending')
                                    <div class="flex gap-2 justify-center">
                                        <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST">@csrf
                                            <button
                                                class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Approve</button>
                                        </form>
                                        <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST">@csrf
                                            <button
                                                class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-xs">{{ $r->status == 'approved' ? 'Approved' : 'Rejected' }} by</p>
                                    <span
                                        class="font-bold text-gray-800 text-xs">{{ $r->approver?->username ?? '-' }}</span>
                                @endif
                            </td>

                            <td class="p-3">
                                <div class="group">
                                    <div class="flex items-center gap-2 remark-display">
                                        <span>{{ $r->remarks ?: '-' }}</span>
                                        <button type="button"
                                            class="hidden group-hover:inline text-blue-600 text-xs remark-edit-btn">✏️</button>
                                    </div>
                                    <form action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                                        class="hidden remark-edit-form mt-1 flex gap-1">@csrf
                                        <input name="remarks" value="{{ $r->remarks }}"
                                            class="border px-2 py-1 rounded text-xs w-28">
                                        <button
                                            class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Save</button>
                                        <button type="button"
                                            class="remark-cancel-btn text-xs text-gray-500 px-1">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MOBILE CARD ROW --}}
                        <tr class="table-row md:hidden">
                            <td colspan="10" class="p-4">
                                <div class="border rounded-lg p-4 shadow-sm space-y-3">

                                    <!-- Employee -->
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $r->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $r->branch?->name ?? '-' }} •
                                            {{ $r->department?->department_name ?? '-' }}</p>
                                    </div>

                                    <!-- Date & Status -->
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

                                    <!-- Clock Sessions -->
                                    <div>
                                        <p class="text-[10px] uppercase font-semibold text-gray-500 mb-1">Sessions</p>
                                        <div class="space-y-2">
                                            @forelse ($r->clocks as $session)
                                                <div class="bg-gray-50 border rounded px-2 py-1 text-xs flex justify-between">
                                                    <div class="">
                                                        <span><strong>In:</strong> {{ $session->clock_in?->format('H:i') ?? '-' }}</span> -
                                                        <span><strong>Out:</strong> {{ $session->clock_out?->format('H:i') ?? '-' }}</span>
                                                    </div>
                                                    <p class="text-blue-700 font-bold text-right">
                                                        {{ $session->total_hm }}</p>
                                                </div>
                                            @empty <span class="text-gray-400">-</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="flex justify-between font-semibold text-sm">
                                        <span class="text-gray-700">Total:</span>
                                        <span class="text-blue-700 font-bold">{{ $r->total_hm }}</span>
                                    </div>

                                    <!-- Reason -->
                                    <div>
                                        <p class="text-[10px] uppercase font-semibold text-gray-500">Reason</p>
                                        <p class="text-xs text-gray-800">{{ $r->reason ?? '-' }}</p>
                                    </div>

                                    <!-- Approval -->
                                    <div>
                                        @if ($r->status === 'pending')
                                            <div class="flex gap-2">
                                                <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST"
                                                    class="w-1/2">@csrf
                                                    <button
                                                        class="bg-green-600 text-white px-3 py-2 rounded text-xs hover:bg-green-700 w-full">Approve</button>
                                                </form>
                                                <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST"
                                                    class="w-1/2">@csrf
                                                    <button
                                                        class="bg-red-600 text-white px-3 py-2 rounded text-xs hover:bg-red-700 w-full">Reject</button>
                                                </form>
                                            </div>
                                        @else
                                            <p class="text-[10px] uppercase font-semibold text-gray-500">Handled by</p>
                                            <p class="font-bold text-gray-800 text-xs">
                                                {{ $r->approver?->username ?? '-' }}</p>
                                        @endif
                                    </div>

                                    <!-- Remarks -->
                                    <div>
                                        <p class="text-[10px] uppercase font-semibold text-gray-500">Remarks</p>
                                        <div class="group">
                                            <div class="flex justify-between items-center remark-display">
                                                <span class="text-xs">{{ $r->remarks ?: '-' }}</span>
                                                <button type="button"
                                                    class="text-blue-600 text-xs remark-edit-btn">✏️</button>
                                            </div>
                                            <form action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                                                class="hidden remark-edit-form flex gap-1 mt-1">@csrf
                                                <input name="remarks" value="{{ $r->remarks }}"
                                                    class="border px-2 py-1 rounded text-xs w-28">
                                                <button
                                                    class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Save</button>
                                                <button type="button"
                                                    class="remark-cancel-btn text-xs text-gray-500 px-1">Cancel</button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="p-6 text-center text-sm text-gray-500">No requests found.</td>
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

@endsection
