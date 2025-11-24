@extends('layouts.app')

@section('content')
<div class="max-w-sm mx-auto text-center mt-10">
    <h2 class="text-2xl font-bold mb-4">Scan QR to Clock In/Out</h2>

    {{-- QR Code --}}
    <div id="qrcode" class="mx-auto w-48 h-48 mb-4"></div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrUrl }}",
            width: 192,
            height: 192,
            colorDark : "#000000",
            colorLight : "#f0f0f0",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>

    {{-- Overtime Details --}}
    <div class="mt-4 text-left">
        <p><strong>Name:</strong> {{ $overtime->name }}</p>
        <p><strong>Branch:</strong> {{ $overtime->branch->name ?? 'N/A' }}</p>
        <p><strong>Department:</strong> {{ $overtime->department->department_name ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
        <p><strong>Reason:</strong> {{ $overtime->reason }}</p>
    </div>
</div>
@endsection
