@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold text-blue-700 mb-8 tracking-tight">
        Overtime Requests
    </h1>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">

            <!-- Branch -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Branch</label>
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
                <label class="block text-sm font-medium text-gray-700">Department</label>
                <select name="department_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">All</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Employee</label>
                <input type="text" name="name" value="{{ request('name') }}"
                    class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- From -->
            <div>
                <label class="block text-sm font-medium text-gray-700">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                    class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- To -->
            <div>
                <label class="block text-sm font-medium text-gray-700">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                    class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-2 mt-2 md:mt-0">
                <button type="submit"
                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-sm text-white rounded-lg shadow-sm">
                    Filter
                </button>

                <a href="{{ route('hr.dashboard') }}"
                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-sm text-gray-700 rounded-lg shadow-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-blue-50 text-gray-700 border-b">
                <tr>
                    <th class="p-3 text-left font-semibold">Date</th>
                    <th class="p-3 text-left font-semibold">Employee</th>
                    <th class="p-3 text-left font-semibold">Clock in/Out</th>
                    <th class="p-3 text-center font-semibold">Total Hours</th>
                    <th class="p-3 text-left font-semibold">Reason</th>
                    <th class="p-3 text-center font-semibold">Status</th>
                    <th class="p-3 font-semibold text-center">Approval</th>
                    <th class="p-3 text-left font-semibold">Remarks</th>
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

                    <tr class="{{ $bg }} border-b hover:bg-gray-100 transition">
                        <td class="p-3">{{ $r->date->format('d M Y') }}</td>
                        <td class="p-3">
                            <div class="space-y-1">
                                <p class="font-semibold text-gray-900 text-sm">{{ $r->name }}</p>
                                <div class="flex items-center space-x-1 text-xs text-gray-500">
                                    <span>{{ $r->branch?->name ?? '-' }}</span>
                                    <span>•</span>
                                    <span>{{ $r->department?->department_name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Sessions --}}
                        <td class="p-3">
                            <div class="space-y-2">
                                @forelse ($r->clocks as $session)
                                    <div class="px-2 py-1 bg-gray-50 rounded-lg border border-gray-200">
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

                        {{-- Total Hours --}}
                        <td class="p-3 font-semibold text-blue-700 text-center">
                            {{ $r->total_hm }}
                        </td>

                        <td class="p-3">{{ $r->reason ?? '-' }}</td>

                        <td class="p-3 text-center">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                        @if ($r->status == 'pending') bg-yellow-200 text-yellow-800
                        @elseif($r->status == 'approved') bg-green-200 text-green-800
                        @else bg-red-200 text-red-800 @endif">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>

                        <td class="p-3 text-center">
                            @if ($r->status === 'pending')
                                <div class="flex gap-2 justify-center">
                                    <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST">
                                        @csrf
                                        <button
                                            class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST">
                                        @csrf
                                        <button class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-700">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Show approver or rejector --}}
                                @if ($r->status === 'approved')
                                    <p class="text-xs">Approved by</p>
                                    <span class="font-semibold text-gray-800">
                                        {{ $r->approver?->username ?? '-' }}
                                    </span>
                                @else
                                    <p class="text-xs">Rejected by</p>
                                    <span class="font-semibold text-gray-800">
                                        {{ $r->approver?->username ?? '-' }}
                                    </span>
                                @endif
                            @endif
                        </td>

                        <td class="p-3 relative group">

                            {{-- Display text mode --}}
                            <div class="flex items-center gap-2 remark-display">
                                <span class="text-gray-800">
                                    {{ $r->remarks ?: '-' }}
                                </span>

                                {{-- Edit icon (hidden until hover) --}}
                                <button type="button"
                                    class="hidden group-hover:inline-block text-blue-600 hover:text-blue-800 text-xs remark-edit-btn">
                                    ✏️
                                </button>
                            </div>

                            {{-- Edit mode --}}
                            <form action="{{ route('hr.overtime.remarks', $r->id) }}" method="POST"
                                class="hidden remark-edit-form mt-1 flex items-center gap-1">
                                @csrf

                                <input type="text" name="remarks" value="{{ $r->remarks }}"
                                    class="border rounded px-2 py-1 text-sm w-32">

                                <button type="submit"
                                    class="bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600">
                                    Save
                                </button>

                                {{-- Cancel --}}
                                <button type="button" class="text-gray-500 text-xs px-2 py-1 remark-cancel-btn">
                                    Cancel
                                </button>
                            </form>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="10" class="text-center p-6 text-gray-500">
                            No requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("td").forEach(cell => {
                const editBtn = cell.querySelector(".remark-edit-btn");
                const displayDiv = cell.querySelector(".remark-display");
                const form = cell.querySelector(".remark-edit-form");
                const cancelBtn = cell.querySelector(".remark-cancel-btn");

                if (!editBtn || !form) return;

                // Enter edit mode
                editBtn.addEventListener("click", () => {
                    displayDiv.classList.add("hidden");
                    form.classList.remove("hidden");
                });

                // Cancel edit mode
                cancelBtn.addEventListener("click", () => {
                    form.classList.add("hidden");
                    displayDiv.classList.remove("hidden");
                });
            });
        });
    </script>

@endsection
