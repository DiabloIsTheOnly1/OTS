@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6 sm:p-8 mt-6">

    <h2 class="text-xl font-bold text-gray-800 mb-6 sm:mb-8 text-center">Overtime Request Details</h2>

    {{-- Messages --}}
    @if(session('success'))
        <div class="mb-5 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- SECTION 1: Staff Information --}}
    <div class="border-b border-gray-200 pb-6 mb-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Staff Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-600 text-xs">Name</p>
                <p class="font-medium text-gray-900 mt-1">{{ $overtime->staff->staff_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-xs">Position</p>
                <p class="font-medium text-gray-900 mt-1">{{ $overtime->staff->position ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-xs">Branch</p>
                <p class="font-medium text-gray-900 mt-1">{{ $overtime->branch->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-xs">Department</p>
                <p class="font-medium text-gray-900 mt-1">{{ $overtime->department->department_name ?? '—' }}</p>
            </div>
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 mt-2">
                <p class="text-gray-600 text-xs">Overtime Date</p>
                <p class="font-medium text-gray-900 mt-1 text-base">
                    {{ $overtime->date->format('l, d F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Overtime Details --}}
    <div class="mb-8">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Overtime Details</h3>

        <!-- Total Hours Requested — Center -->
        <div class="text-center mb-6">
            <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Total Hours Requested</p>
            <p class="text-4xl font-bold text-blue-700 mt-2">
                @if($overtime->total_hours > 0)
                    @php
                        $h = floor($overtime->total_hours);
                        $m = round(($overtime->total_hours - $h) * 60);
                    @endphp
                    {{ $h }}<span class="text-2xl text-blue-600">h</span>
                    @if($m > 0)
                        <span class="ml-1">{{ $m }}<span class="text-xl text-blue-600">m</span></span>
                    @endif
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </p>
        </div>

        <!-- Type of Work | Reg No — Side by side, top-aligned -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
    <div>
        <p class="text-gray-500 text-xs font-medium">Type of Work</p>
        <div class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg min-h-[90px] text-left flex flex-col justify-start">
            <p class="text-gray-800 leading-relaxed ">
                {{ $overtime->type_of_work ?? '—' }}
            </p>
        </div>
    </div>
    <div>
        <p class="text-gray-600 text-xs font-medium">
            Reg No 
            <span class="text-xs text-gray-500 lowercase">(After Sales only)</span>
        </p>
        <div class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg min-h-[90px] text-left flex flex-col justify-start">
            <p class="text-gray-800 leading-relaxed">
                {{ $overtime->reg_no ?? 'N/A' }}
            </p>
        </div>
    </div>
</div>


    {{-- Clock In / Out Buttons --}}
    <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4 border-t">
        <form action="{{ route('clock.in', $overtime->id) }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-medium px-10 py-3 rounded-lg shadow transition">
                Clock In
            </button>
        </form>
        <form action="{{ route('clock.out', $overtime->id) }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium px-10 py-3 rounded-lg shadow transition">
                Clock Out
            </button>
        </form>
    </div>

    <div class="mt-6 text-center flex flex-col sm:flex-row justify-center gap-4">
        <a href="{{ route('overtime.clock.details', $overtime->id) }}"
           class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium px-10 py-3 rounded-lg shadow transition">
            View Clock Details
        </a>
    </div>

</div>
@endsection
