@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Overtime Request Details</h2>

    {{-- Messages --}}
    @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 mb-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Details --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6 mb-8 text-sm">

    <!-- First row -->
    <div>
        <p class="font-bold text-gray-700">Name</p>
        <p class="text-gray-900 mt-1">{{ $overtime->staff->staff_name ?? 'N/A' }}</p>
    </div>

    <div>
        <p class="font-bold text-gray-700">Position</p>
        <p class="text-gray-900 mt-1">{{ $overtime->staff->position ?? 'N/A' }}</p>
    </div>

    <!-- Second row -->
    <div>
        <p class="font-bold text-gray-700">Branch</p>
        <p class="text-gray-900 mt-1">{{ $overtime->branch->name ?? 'N/A' }}</p>
    </div>

    <div>
        <p class="font-bold text-gray-700">Department</p>
        <p class="text-gray-900 mt-1">{{ $overtime->department->department_name ?? 'N/A' }}</p>
    </div>

    <!-- Third row -->
    <div>
        <p class="font-bold text-gray-700">Date</p>
        <p class="text-gray-900 mt-1">{{ $overtime->date->format('d M Y') }}</p>
    </div>

    <!-- Empty cell to keep the grid balanced (optional) -->
    <div></div>

    <!-- Fourth row – Type of Work & Reg No (full width section) -->
    <!-- Overtime Details Section -->
        <div class="sm:col-span-2">
            <div class="bg-gradient-to-br from-amber-50/80 to-orange-50/60 backdrop-blur-sm rounded-2xl border border-amber-200/60 shadow-lg overflow-hidden">
                
                <div class="p-6 pb-8 space-y-8">

                    <!-- Row 1: Planned Schedule + Total Hours -->
                    <div class="text-center md:text-left">
                        <h3 class="text-lg font-bold text-amber-900 mb-4 tracking-tight">
                            Planned Overtime Schedule
                        </h3>

                        <div class="flex flex-col sm:flex-row items-center justify-center md:justify-start gap-6 md:gap-10">
                            <!-- Start → End -->
                            <div class="flex items-center gap-5">
                                <div class="text-center">
                                    <span class="text-4xl font-extrabold text-amber-800 tracking-tight">
                                        {{ $overtime->start_time?->format('H:i') ?? '—' }}
                                    </span>
                                </div>

                                <span class="text-5xl font-thin text-amber-500 hidden xs:block">→</span>
                                <span class="text-4xl font-thin text-amber-500 xs:hidden">↓</span>

                                <div class="text-center">
                                    <span class="text-4xl font-extrabold text-amber-800 tracking-tight">
                                        {{ $overtime->end_time?->format('H:i') ?? '—' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Total Hours Badge -->
                            <div class="flex items-center">
                                <span class="text-sm font-semibold text-amber-700 uppercase tracking-wider mr-4 hidden sm:inline">
                                    Total Hours
                                </span>
                                <div class="bg-amber-700 text-white font-bold px-5 py-2.5 rounded-full shadow-md text-lg min-w-[110px] text-center">
                                    @if ($overtime->total_hours > 0)
                                        @php
                                            $h = floor($overtime->total_hours);
                                            $m = round(($overtime->total_hours - $h) * 60);
                                        @endphp
                                        {{ $h }}<small class="text-xs opacity-90">h</small>
                                        @if($m > 0) {{ $m }}<small class="text-xs opacity-90">m</small>@endif
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Type of Work + Reg No -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-center sm:text-left">
                        <div>
                            <p class="text-xs font-bold text-gray-900 ">
                                Type of Work
                            </p>
                            <p class="mt-2 text-xl font-medium text-gray-900">
                                {{ $overtime->type_of_work ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-900 ">
                                Reg No 
                                <span class="text-xs font-normal text-gray-500 lowercase tracking-normal">
                                    (After Sales only)
                                </span>
                            </p>
                            <p class="mt-2 text-xl font-medium text-gray-900">
                                {{ $overtime->reg_no ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- @if($overtime->clocks)
            <p><span class="font-semibold text-gray-700">Clock In:</span><br>
                <span class="text-gray-900">{{ $overtime->clocks->clock_in?->format('H:i') ?? 'Not yet' }}</span>
            </p>

            <p><span class="font-semibold text-gray-700">Clock Out:</span><br>
                <span class="text-gray-900">{{ $overtime->clocks->clock_out?->format('H:i') ?? 'Not yet' }}</span>
            </p>

            <p class="sm:col-span-2">
                <span class="font-semibold text-gray-700">Total Time:</span><br>
                <span class="text-gray-900">{{ $overtime->clocks->total_hm ?? '0' }} hours</span>
            </p>
        @endif --}}

    </div>

    {{-- Buttons --}}
    <div class="flex  justify-center gap-4 mt-4">

        <form action="{{ route('clock.in', $overtime->id) }}" method="POST">
            @csrf
            <button class="w-full sm:w-auto bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                Clock In
            </button>
        </form>

        <form action="{{ route('clock.out', $overtime->id) }}" method="POST">
            @csrf
            <button class="w-full sm:w-auto bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                Clock Out
            </button>
        </form>

    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('overtime.index') }}"
           class="text-blue-600 hover:underline text-sm">
            Back to Request List
        </a>
    </div>

</div>

@endsection
