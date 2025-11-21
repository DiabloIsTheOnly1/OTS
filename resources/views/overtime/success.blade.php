@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-lg border rounded-2xl p-8 text-center">

        <div class="flex justify-center mb-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-green-700 mb-2">Request Submitted!</h1>
        <div id="qrcode" class="mx-auto w-48 h-48 cursor-pointer" title="Scan to Clock In"></div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script>
    const overtimeId = "{{ $overtime->id }}";
    const clockinUrl = "{{ route('overtime.clockin', $overtime->id) }}";

    // Generate QR
    new QRCode(document.getElementById("qrcode"), {
        text: clockinUrl,
        width: 192,
        height: 192,
        colorDark : "#000000",
        colorLight : "#f0f0f0",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Optional: desktop click for testing
    document.getElementById("qrcode").addEventListener("click", () => {
        fetch(clockinUrl)
            .then(res => res.json())
            .then(data => alert(data.message))
            .catch(err => alert("Error clocking in"));
    });
    </script>

            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Name:</strong> {{ $overtime->name }}</p>
                <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('overtime.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Submit Another Request
            </a>

            <a href="/" class="text-gray-500 hover:text-gray-700 underline text-sm">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- QRCode.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script>
    const overtimeId = "{{ $overtime->id }}"; // Pass OT ID from backend
    new QRCode(document.getElementById("qrcode"), {
        text: "OT-" + overtimeId,
        width: 192,
        height: 192,
        colorDark : "#000000",
        colorLight : "#f0f0f0",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>
@endsection
