@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white shadow-lg border rounded-2xl p-8 text-center">

        <div class="flex justify-center mb-4">
            <div class="{{ $clock->clock_out && $message == 'Clocked Out' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }} p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    @if($clock->clock_out && $message == 'Clocked Out')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    @endif
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold {{ $clock->clock_out && $message == 'Clocked Out' ? 'text-blue-700' : 'text-green-700' }} mb-2">
            {{ $message }}
        </h1>

        <p class="text-gray-700">
            @if($message == 'You have already clocked out')
                Last clock out time:
            @else
                Time:
            @endif
        </p>
        <p class="text-2xl font-semibold mt-1">{{ $scannedAt->format('d M Y H:i:s') }}</p>

        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Name:</strong> {{ $overtime->name }}</p>
            <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
            @if($clock->total_time_taken)
                <p><strong>Total Time:</strong> {{ gmdate('H:i:s', $clock->total_time_taken) }}</p>
            @endif
        </div>

        <div class="mt-6">
        <a href="{{ route('hr.dashboard') }}" class="text-blue-600 hover:underline text-sm">
        Back to Dashboard
         </a>
        </div>

        </div>
    </div>
</div>
@endsection
