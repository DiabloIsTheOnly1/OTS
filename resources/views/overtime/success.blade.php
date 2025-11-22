@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto my-8">
    <div class="bg-white shadow-lg border rounded-2xl p-8 text-center">

        {{-- Success Icon --}}
        <div class="flex justify-center mb-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl font-bold text-green-700 mb-4">Request Submitted!</h1>

        {{-- QR CODE --}}
        <div id="qrcode" class="mx-auto w-48 h-48 cursor-pointer mb-4" title="Scan to Clock In / Clock Out"></div>

        <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
        <script>
            const clockUrl = "{{ route('overtime.clock', $overtime->id) }}";

            new QRCode(document.getElementById("qrcode"), {
                text: clockUrl,
                width: 192,
                height: 192,
                colorDark : "#000000",
                colorLight : "#f0f0f0",
                correctLevel : QRCode.CorrectLevel.H
            });
        </script>

        {{-- User Info --}}
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Name:</strong> {{ $overtime->name }}</p>
            <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
        </div>

    </div>

    {{-- Action Buttons --}}
    <div class="mt-6 flex flex-col gap-3">
        <a href="{{ route('overtime.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition text-center">
            Submit Another Request
        </a>

        <a href="{{ route('hr.dashboard') }}"
           class="text-blue-600 hover:underline text-center text-sm">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
