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
                    <th class="p-3 text-left font-semibold">Branch</th>
                    <th class="p-3 text-left font-semibold">Name</th>
                    <th class="p-3 text-left font-semibold">Dept</th>
                    <th class="p-3 text-left font-semibold">Clock In</th>
                    <th class="p-3 text-left font-semibold">Clock Out</th>
                    <th class="p-3 text-left font-semibold">Hours</th>
                    <th class="p-3 text-left font-semibold">Reason</th>
                    <th class="p-3 text-left font-semibold">Status</th>
                    <th class="p-3 text-left font-semibold">Approved By</th>
                    <th class="p-3 text-left font-semibold text-center">Actions</th>
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
                        <td class="p-3">{{ $r->branch->name }}</td>
                        <td class="p-3 font-semibold text-gray-800">{{ $r->name }}</td>
                        <td class="p-3">{{ $r->department?->department_name ?? '-' }}</td>

                        {{-- Clock In --}}
                        <td class="p-3">
                            {{ $r->clock?->clock_in ? $r->clock->clock_in->format('H:i') : '-' }}
                        </td>

                        {{-- Clock Out --}}
                        <td class="p-3">
                            {{ $r->clock?->clock_out ? $r->clock->clock_out->format('H:i') : '-' }}
                        </td>

                        <td class="p-3">{{ $r->clock?->total_hm ?? '-' }}</td>
                        <td class="p-3">{{ $r->reason ?? '-' }}</td>

                        <td class="p-3">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                        @if ($r->status == 'pending') bg-yellow-200 text-yellow-800
                        @elseif($r->status == 'approved') bg-green-200 text-green-800
                        @else bg-red-200 text-red-800 @endif">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>

                        <td class="p-3">{{ $r->approver?->username ?? '-' }}</td>

                        <td class="p-3 text-center">
                            @if (auth()->check() && $r->status == 'pending')
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
                                <span class="text-gray-400 text-xs">No actions</span>
                            @endif
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
@endsection
