@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold text-blue-700 mb-6">HR Dashboard - Overtime Requests</h1>

{{-- Filters --}}
<form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <input type="text" name="search" placeholder="Search employee name"
           value="{{ request('search') }}"
           class="border p-2 rounded">

    <select name="status" class="border p-2 rounded">
        <option value="">All Status</option>
        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
    </select>

    <button class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Filter</button>
</form>

{{-- Table --}}
<div class="bg-white shadow rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-gray-700">
        <tr>
            <th class="p-2">Date</th>
            <th class="p-2">Name</th>
            <th class="p-2">Dept</th>
            <th class="p-2">Start</th>
            <th class="p-2">End</th>
            <th class="p-2">Hours</th>
            <th class="p-2">Reason</th>
            <th class="p-2">Status</th>
            <th class="p-2">Approved By</th>
            <th class="p-2">Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse($requests as $r)
            <tr class="@if($r->status=='pending') bg-yellow-100 @elseif($r->status=='approved') bg-green-100 @else bg-red-100 @endif">
                <td class="p-2">{{ $r->date }}</td>
                <td class="p-2">{{ $r->employee_name }}</td>
                <td class="p-2">{{ $r->department?->name ?? '-' }}</td>
                <td class="p-2">{{ $r->clock_in_time ? $r->clock_in_time->format('H:i') : '-' }}</td>
                <td class="p-2">{{ $r->clock_out_time ? $r->clock_out_time->format('H:i') : '-' }}</td>
                <td class="p-2">{{ $r->total_hours ?? '-' }}</td>
                <td class="p-2">{{ $r->work_description ?? '-' }}</td>
                <td class="p-2 capitalize">{{ $r->status }}</td>
                <td class="p-2">{{ $r->approver?->username ?? '-' }}</td>
                <td class="p-2">
                    @if(auth()->check() && $r->status=='pending')
                        <form action="{{ route('hr.overtime.approve', $r->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button class="bg-green-600 text-white px-2 py-1 rounded text-xs">Approve</button>
                        </form>

                        <form action="{{ route('hr.overtime.reject', $r->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">Reject</button>
                        </form>
                    @else
                        <span class="text-gray-500 text-xs">No actions</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center p-4 text-gray-500">No requests found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
