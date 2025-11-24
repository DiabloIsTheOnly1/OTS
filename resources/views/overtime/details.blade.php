@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-xl p-6">

    <h2 class="text-xl font-bold mb-4">Overtime Request Details</h2>

    {{-- Messages --}}
    @if(session('success'))
        <div class="p-3 mb-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="p-3 mb-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    {{-- Details --}}
    <div class="mb-6">
    <p><strong>Name:</strong> {{ $overtime->name }}</p>
    <p><strong>Branch:</strong> {{ $overtime->branch->name ?? 'N/A' }}</p>
    <p><strong>Department:</strong> {{ $overtime->department->department_name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
    <p><strong>Reason:</strong> {{ $overtime->reason }}</p>

    @if($overtime->clock)
        <p><strong>Clock In:</strong> {{ $overtime->clock->clock_in ?? 'Not yet' }}</p>
        <p><strong>Clock Out:</strong> {{ $overtime->clock->clock_out ?? 'Not yet' }}</p>
        <p><strong>Total:</strong> {{ $overtime->clock->total_hm ?? '0' }} hours</p>
    @endif
</div>

    {{-- QR --}}
    {{-- <div class="text-center mb-6">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($qrUrl) }}" class="mx-auto">
        <p class="text-sm mt-2">Scan to view this page</p>
    </div> }}--]]

    {{-- Buttons --}}
    <div class="flex gap-4 justify-center">

        <form action="{{ route('clock.in', $overtime->id) }}" method="POST">
            @csrf
            <button class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">
                Clock In
            </button>
        </form>

        <form action="{{ route('clock.out', $overtime->id) }}" method="POST">
            @csrf
            <button class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
                Clock Out
            </button>
        </form>

    </div>

</div>
@endsection
