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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4 mb-8">

        <p><span class="font-semibold text-gray-700">Name:</span><br>
            <span class="text-gray-900">{{ $overtime->name }}</span>
        </p>

        <p><span class="font-semibold text-gray-700">Branch:</span><br>
            <span class="text-gray-900">{{ $overtime->branch->name ?? 'N/A' }}</span>
        </p>

        <p><span class="font-semibold text-gray-700">Department:</span><br>
            <span class="text-gray-900">{{ $overtime->department->department_name ?? 'N/A' }}</span>
        </p>

        <p><span class="font-semibold text-gray-700">Date:</span><br>
            <span class="text-gray-900">{{ $overtime->date->format('d M Y') }}</span>
        </p>

         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
            <p><span class="font-semibold text-gray-700">Work done for the day:</span><br>
                <span class="text-gray-900">{{ $overtime->work_done }}</span>
            </p>
      
            <p><span class="font-semibold text-gray-700">Work to be completed during OT & _/HR:</span><br>
                <span class="text-gray-900">{{ $overtime->reason }}</span>
            </p>
        </div>

        @if($overtime->clock)
            <p><span class="font-semibold text-gray-700">Clock In:</span><br>
                <span class="text-gray-900">{{ $overtime->clock->clock_in ?? 'Not yet' }}</span>
            </p>

            <p><span class="font-semibold text-gray-700">Clock Out:</span><br>
                <span class="text-gray-900">{{ $overtime->clock->clock_out ?? 'Not yet' }}</span>
            </p>

            <p class="sm:col-span-2">
                <span class="font-semibold text-gray-700">Total Time:</span><br>
                <span class="text-gray-900">{{ $overtime->clock->total_hm ?? '0' }} hours</span>
            </p>
        @endif

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

</div>

@endsection
