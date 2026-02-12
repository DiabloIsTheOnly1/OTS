@extends('layouts.app')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Overtime Requests</title>

        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#3498db',
                            secondary: '#2c3e50',
                            success: '#2ecc71',
                            danger: '#e74c3c',
                            warning: '#f39c12',
                        }
                    }
                }
            }
        </script>

    </head>

    <body class="bg-gray-50 min-h-screen">

        <div class="container mx-auto px-4 py-8 max-w-10xl">

            <header
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-6 bg-white rounded-xl shadow-sm">
                <div>
                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        <i class="fas fa-business-time text-blue-500 mr-3"></i>
                        Overtime Requests
                    </h1>

                    <div class="text-sm text-gray-500 mt-1 block md:hidden">
                        {{ now()->format('l, d M Y') }}
                    </div>

                    <!-- Subtitle -->
                    <p class="text-gray-600 mt-2 text-sm md:text-base">Manage employee overtime requests</p>
                </div>

                {{-- Old New Request --}}
                {{-- <a href="{{ route('overtime.create') }}"
                    class="mt-4 md:mt-0 inline-flex items-center justify-center px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition w-full md:w-auto">
                    <i class="fas fa-plus-circle mr-2"></i> New Request
                </a> --}}

            </header>

            <!-- Branch / Department Summary -->
            {{-- <div class="mb-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Branch Card -->
                <div class="bg-white shadow-sm rounded-xl p-4 border border-gray-100 flex items-center space-x-3">
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                        <i class="fas fa-building text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-500 uppercase">Branch</p>
                        <p class="text-base md:text-lg font-semibold text-gray-800">{{ $branch->name }}</p>
                    </div>
                </div>

                <!-- Department Card -->
                <div class="bg-white shadow-sm rounded-xl p-4 border border-gray-100 flex items-center space-x-3">
                    <div class="p-3 rounded-lg bg-green-50 text-green-600">
                        <i class="fas fa-sitemap text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-500 uppercase">Department</p>
                        <p class="text-base md:text-lg font-semibold text-gray-800">{{ $department->department_name }}</p>
                    </div>
                </div>
            </div> --}}

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-4">
                <form method="GET" action="{{ route('overtime.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 md:gap-4 items-end">

                    <input type="hidden" name="employee_id" value="{{ $staffId }}">
                    
                    <!-- Branch -->
                    {{-- <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        <option value="">All</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                    <!-- Employee -->
                    {{-- <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700">Employee</label>
                        <input type="text" name="name" value="{{ request('name') }}"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div> --}}

                    <!-- From -->
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700">From</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>

                    <!-- To -->
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700">To</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700">Status</label>
                        <select name="status"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-2 w-full">
                        <button type="submit"
                            class="flex-1 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm text-sm md:text-base">
                            Filter
                        </button>

                        <a href="{{ route('overtime.index', ['employee_id' => request('employee_id')]) }}"
                            class="flex-1 text-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg shadow-sm text-sm md:text-base">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- ===== TABLE SECTION ===== -->
            <div class="rounded-lg shadow overflow-hidden">

                <!-- MOBILE CLEAN VIEW -->
                <div class="block md:hidden space-y-2">

                    @foreach ($requests as $req)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 space-y-2">

                            {{-- Employee --}}
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $req->staff->staff_name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $req->branch->name ?? '-' }} • {{ $req->department->department_name ?? '-' }}
                                </p>
                            </div>

                            {{-- Meta Row --}}
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">
                                    {{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}
                                </span>

                                @if ($req->status === 'pending')
                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Approved</span>
                                @elseif($req->status === 'rejected')
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-600">Rejected</span>
                                @endif
                            </div>

                            {{-- Clock Sessions --}}
                            @if ($req->clocks->count())
                                <div class="space-y-2">
                                    @foreach ($req->clocks as $session)
                                        <div
                                            class="flex justify-between items-center bg-gray-50 rounded-xl px-3 py-2 text-sm">
                                            <div class="text-gray-700">
                                                {{ $session->clock_in?->format('H:i') ?? '-' }}
                                                —
                                                {{ $session->clock_out?->format('H:i') ?? '-' }}
                                                @if ($session->auto_flag)
                                                    <span class="text-xs italic text-orange-500 ml-1">(Auto)</span>
                                                @endif
                                            </div>

                                            <span class="text-xs font-semibold text-blue-600">
                                                {{ $session->total_hm }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @php
                                // APPROVED HOURS (if approved)
                                if ($req->approved_hours !== null) {
                                    $apprHours = floor($req->approved_hours);
                                    $apprMinutes = round(($req->approved_hours - $apprHours) * 60);
                                    $req->approved_hm = sprintf('%02d:%02d', $apprHours, $apprMinutes);
                                }
                            @endphp
                            {{-- Hours Summary --}}
                            <div class="grid grid-cols-3 text-center gap-2">
                                <div>
                                    <p class="text-xs text-gray-400">Requested</p>
                                    <p class="text-sm font-semibold text-amber-700">
                                        {{ $req->requested_hm ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Actual</p>
                                    <p class="text-sm font-semibold text-blue-700">
                                        {{ $req->actual_hm }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Approved</p>
                                    <p class="text-sm font-semibold text-purple-700">
                                        @if ($req->approved_hm)
                                            {{ $req->approved_hm }}
                                        @elseif ($req->status === 'approved')
                                            {{ $req->actual_hm }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            @if ($req->remarks)
                                <div class="text-sm text-gray-600 leading-relaxed border-l-2 border-gray-200 pl-3">
                                    {{ $req->remarks }}
                                </div>
                            @endif

                            {{-- Action --}}
                            <div class="flex justify-end pt-2">
                                <a href="{{ route('overtime.success', $req->id) }}"
                                    class="px-2 py-1 rounded inline-flex items-center text-sm font-medium bg-blue-100 text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-qrcode mr-1"></i> QR
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>


                <!-- DESKTOP RESPONSIVE + SCROLLABLE ON SMALL -->
                <div class="overflow-x-auto">
                    <table class="bg-white  min-w-[600px] md:min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 whitespace-nowrap">
                            <tr class="hidden md:table-row">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In/Out
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested
                                    Hours</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actual Hours
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remark</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($requests as $req)
                                <tr class="hover:bg-gray-50 transition hidden md:table-row">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        {{ $req->staff->staff_name ?? '-' }}
                                        <div class="text-xs text-gray-600">
                                            {{ $req->branch->name ?? '-' }} •
                                            {{ $req->department->department_name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}</td>
                                    <!-- Clock In/Out -->
                                    <td class="p-3">
                                        <div class="space-y-1">
                                            @forelse ($req->clocks as $session)
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
                                            {{ $req->requested_hm ?? '-' }}
                                        </span>
                                    </td>

                                    @php
                                        // APPROVED HOURS (if approved)
                                        if ($req->approved_hours !== null) {
                                            $apprHours = floor($req->approved_hours);
                                            $apprMinutes = round(($req->approved_hours - $apprHours) * 60);
                                            $req->approved_hm = sprintf('%02d:%02d', $apprHours, $apprMinutes);
                                        }
                                    @endphp
                                    <!-- Total -->
                                    <td class="p-3 text-center">

                                        {{-- Always show actual --}}
                                        <div class="font-bold text-blue-700 text-sm">
                                            {{ $req->actual_hm }}
                                        </div>

                                        @if ($req->status === 'approved')
                                            @if ($req->approved_hm)
                                                <div class="text-purple-700 text-xs font-semibold">
                                                    Approved: {{ $req->approved_hm }}
                                                    {{-- <span class="text-purple-500 font-bold">(Partial)</span> --}}
                                                </div>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm whitespace-nowrap text-center">
                                        @if ($req->status === 'pending')
                                            <span
                                                class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Pending</span>
                                        @elseif($req->status === 'approved')
                                            <span
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Approved</span>
                                        @elseif($req->status === 'rejected')
                                            <span
                                                class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full">Rejected</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm whitespace-pre-line break-words max-w-52">
                                        {{ $req->remarks ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('overtime.success', $req->id) }}"
                                            class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-xs inline-flex items-center whitespace-nowrap">
                                            <i class="fas fa-qrcode mr-1"></i> QR
                                        </a>
                                        <a href="{{ route('hr.overtime.view', $req->id) }}"
                                            class="px-2 py-1 rounded text-xs inline-flex items-center whitespace-nowrap">
                                            <i class="fa-solid fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center">
                {{ $requests->links('pagination::tailwind') }}
            </div>

        </div>

    </body>

    </html>
@endsection
