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

        <div class="container mx-auto px-4 py-8 max-w-7xl">

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

                <a href="{{ route('overtime.create') }}"
                    class="mt-4 md:mt-0 inline-flex items-center justify-center px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition w-full md:w-auto">
                    <i class="fas fa-plus-circle mr-2"></i> New Request
                </a>
            </header>

            <!-- Branch / Department Summary -->
            <div class="mb-2 grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>

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
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700">Employee</label>
                        <input type="text" name="name" value="{{ request('name') }}"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>

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

                        <a href="{{ route('overtime.index') }}"
                            class="flex-1 text-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg shadow-sm text-sm md:text-base">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- ===== TABLE SECTION ===== -->
            <div class="bg-white rounded-lg shadow overflow-hidden">

                <!-- MOBILE STACKED VIEW -->
                <div class="block md:hidden">
                    @foreach ($requests as $req)
                        <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition space-y-2">

                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500 uppercase">Employee</span>
                                <span class="text-sm font-bold text-gray-900">{{ $req->name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500 uppercase">Branch</span>
                                <span class="text-sm">{{ $req->branch->name ?? '-' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500 uppercase">Date</span>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500 uppercase">Total Time</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $req->total_hm ?? '00:00' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500 uppercase">Status</span>
                                <div>
                                    @if ($req->status === 'pending')
                                        <span
                                            class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Pending</span>
                                    @elseif($req->status === 'approved')
                                        <span
                                            class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full">Rejected</span>
                                    @endif
                                </div>
                            </div>

                            <!-- ACTION BUTTON -->
                            <div class="flex justify-end pt-2">
                                <a href="{{ route('overtime.success', $req->id) }}"
                                    class="px-3 py-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-xs inline-flex items-center">
                                    <i class="fas fa-qrcode mr-1"></i> QR
                                </a>
                            </div>

                            <div class="text-xs text-gray-600 text-right">
                                Department: {{ $req->department->department_name ?? '-' }}
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- DESKTOP RESPONSIVE + SCROLLABLE ON SMALL -->
                <div class="overflow-x-auto">
                    <table class="min-w-[600px] md:min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 whitespace-nowrap">
                            <tr class="hidden md:table-row">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Branch</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In/Out</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Time</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($requests as $req)
                                <tr class="hover:bg-gray-50 transition hidden md:table-row">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        {{ $req->name }}
                                        <div class="text-xs text-gray-600">Dept:
                                            {{ $req->department->department_name ?? '-' }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-sm whitespace-nowrap text-center">
                                        {{ $req->branch->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="text-gray-700">
                                            <span class="font-medium">In: </span>{{ $req->clock_in_display }} - 
                                            <span class="font-medium">Out: </span>{{ $req->clock_out_display }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap text-center">
                                        <div class="font-medium text-blue-700">{{ $req->total_hm ?? '00:00' }}</div>
                                        {{-- <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex items-center space-x-1">
                                                    <span class="font-medium text-gray-600">In:</span>
                                                    <span class="text-gray-900">{{ $req->clock_in_display }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="font-medium text-gray-600">Out:</span>
                                                    <span class="text-gray-900">{{ $req->clock_out_display }}</span>
                                                </div>
                                            </div>
                                            <div class="font-medium text-blue-600 text-xs bg-blue-50 px-2 py-1 rounded">
                                                {{ $req->total_hm ?? '00:00' }}
                                            </div>
                                        </div> --}}
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

                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('overtime.success', $req->id) }}"
                                            class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-xs inline-flex items-center whitespace-nowrap">
                                            <i class="fas fa-qrcode mr-1"></i> QR
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
