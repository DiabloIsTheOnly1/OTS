@extends('layouts.app')

@section('content')
    <style>
        @media print {

            nav,
            header,
            .navbar,
            .topbar,
            .navigation,
            .main-header,
            .header,
            body>div:first-child,
            .no-print {
                display: none !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body,
            html {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .print-page {
                width: 100% !important;
                margin: 0 auto !important;
                padding: 20px !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            #qrcode {
                display: flex !important;
                width: 250px !important;
                height: 250px !important;
                justify-content: center !important;
            }
        }
    </style>

    <div class="mx-auto  max-w-4xl flex flex-col sm:flex-row gap-4 sm:items-center justify-between mb-6 px-4 sm:pc-0 print:hidden">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 text-left">
                <i class="fas fa-qrcode text-blue-500 mr-3"></i>
                Overtime QR Code
            </h1>
            <p class="text-gray-600">
                Scan the QR code below to clock in or out for your overtime.
            </p>
        </div>
        <div class="mt-4 sm:mt-2 w-full sm:w-auto">
            <a href="{{ url()->previous() }}"
                class="w-full sm:w-auto justify-center inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-5 py-3 sm:py-2 rounded-xl hover:bg-gray-300 transition-all font-bold text-base sm:text-lg shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </div>
    
    <div class="flex items-center justify-center bg-gray-50 py-4 px-4 mx-auto max-w-4xl">
        <div class="w-full max-w-md">
            @if (session('submitted'))
                <div class="text-center mb-8 print:hidden">
                    <div class="flex justify-center mb-4">
                        <div class="bg-green-100 text-green-600 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold text-gray-800 mb-2">
                        Request Submitted
                    </h1>
                </div>
            @endif

            <!-- QR Code Card -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <!-- QR Code -->
                <div id="qrcode" class="mx-auto w-64 h-64 mb-6"></div>

                <!-- Info -->
                <div class="space-y-3 mb-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Name</div>
                        <div class="font-medium text-gray-800">{{ $overtime->staff->staff_name }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Date</div>
                        <div class="font-medium text-gray-800">{{ $overtime->date->format('d M Y') }}</div> 
                    </div>

                    <div class="text-center">
                        <div class="text-sm text-gray-500">Requested Hours</div>
                        <div class="font-medium text-gray-800">
                            @if($overtime->total_hours > 0)
                    @php
                        $h = floor($overtime->total_hours);
                        $m = round(($overtime->total_hours - $h) * 60);
                    @endphp
                    {{ $h }}h
                    @if($m > 0)
                        {{ $m }}m
                    @endif
                @else
                    0h
                @endif
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 my-4 print:hidden"></div>

                <!-- Actions -->
                <div class="print:hidden space-y-3">
                    <button onclick="window.print()"
                        class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors">
                        Print This Page
                    </button>

                    <a href="{{ route('overtime.create') }}"
                        class="block text-center text-gray-600 hover:text-gray-800 text-sm transition-colors">
                        Submit Another Request
                    </a>
                </div>
            </div>

            <!-- Print Note -->
            <div class="mt-6 text-center text-gray-400 text-xs print:hidden">
                <p>Save this page for reference</p>
            </div>
        </div>
    </div>

    <!-- QR Code Script -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrUrl }}",
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Auto-refresh after 60 seconds
        setTimeout(() => {
            window.location.reload();
        }, 60000);
    </script>
@endsection
