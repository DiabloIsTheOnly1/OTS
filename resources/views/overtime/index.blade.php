<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overtime Requests</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    <style>
        .req-row:hover {
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    @include('layouts.topbar')

    <div class="container mx-auto px-4 py-8 max-w-7xl">

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-6 bg-white rounded-xl shadow-sm">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-business-time text-blue-500 mr-3"></i>
                    Overtime Requests
                    <span class="ml-2 text-sm text-gray-500">({{ now()->format('l, d M Y') }})</span>
                </h1>
                <p class="text-gray-600 mt-2">Manage employee overtime requests</p>
            </div>

            <a href="{{ route('overtime.create') }}"
               class="mt-4 md:mt-0 inline-flex items-center px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                <i class="fas fa-plus-circle mr-2"></i> New Request
            </a>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
            <form method="GET" action="{{ route('overtime.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                <!-- Branch -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
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
                    <select name="status"
                            class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        <option value="pending"  {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
                        <option value="approved" {{ request('status')=='approved'? 'selected':'' }}>Approved</option>
                        <option value="rejected" {{ request('status')=='rejected'? 'selected':'' }}>Rejected</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-2">
                    <button type="submit"
                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-sm text-white rounded-lg shadow-sm">
                        Filter
                    </button>

                    <a href="{{ route('overtime.index') }}"
                       class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-sm text-gray-700 rounded-lg shadow-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @foreach($requests as $req)
                        <tr class="req-row hover:bg-gray-50">
                            <!-- Employee -->
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                {{ $req->name }}
                                <div class="text-xs text-gray-600">Department: {{ $req->department->department_name ?? '-' }}</div>
                            </td>

                            <!-- Branch -->
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $req->branch->name ?? '-' }}
                            </td>

                            <!-- Date -->
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}
                            </td>

                            <!-- Total Time -->
                            <td class="px-4 py-3 text-sm font-medium text-blue-700">
                                {{ $req->total_time_taken ? $req->total_time_taken . ' min' : '-' }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 text-sm">
                                @if($req->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Approved</span>
                                @elseif($req->status === 'rejected')
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Rejected</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-sm font-medium">
                                <div class="flex items-center space-x-2">

                                    <a href="{{ route('overtime.show', $req->id) }}"
                                       class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-xs">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>

                                    <a href="{{ route('overtime.edit', $req->id) }}"
                                       class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 text-xs">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 mt-4 flex justify-center space-x-1">
            {{ $requests->links('pagination::tailwind') }}
        </div>

    </div>
</body>
</html>
