@extends('layouts.app')

@section('content')
    <!-- Prevent overlap with topbar on mobile -->
    <div class="pt-[10px] sm:pt-6 lg:pt-4 transition-all mx-0 lg:mx-[20px]">

        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h1 class="text-3xl font-bold text-blue-700 tracking-tight">
                    Overtime Requests
                </h1>
                <p class="text-sm text-gray-500 mt-1 hidden sm:block">
                    {{ now()->format('l, d F Y') }}
                </p>
                <p class="text-sm text-gray-500 mt-1 sm:hidden">
                    {{ now()->format('D, d M Y') }}
                </p>
            </div>

            @canAccess('manage_request')
            <a href="{{ route('overtime.create') }}"
                class="inline-flex items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 px-4 py-2 font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">

                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">New Overtime Request</span>
                    <span class="sm:hidden">New Request</span>
                </span>

                <!-- Shine effect on hover -->
                <span
                    class=" inset-0 -translate-x-full bg-white/20 skew-x-12 transition-transform duration-700 group-hover:translate-x-full"></span>
            </a>
            @endcanAccess
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
            <form method="GET" id="filter-form" action="{{ route('hr.dashboard') }}"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end">

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Branch</label>
                    <select name="branch_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Department</label>
                    <select name="department_id" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Employee</label>
                    <input type="text" name="name" value="{{ request('name') }}"
                        class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- From -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">From</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- To -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">To</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                {{-- Month --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Month</label>
                    <input type="month" name="month" value="{{ request('month') }}"
                        class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase">Status</label>
                    <select name="status" class="mt-1 px-3 py-1 border w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Buttons: Always Horizontal -->
                <div class="col-span-1 sm:col-span-2 md:col-span-6 flex items-center gap-2 mb-4">

                    <!-- Apply Filter -->
                    <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition">
                        Apply Filter
                    </button>

                    <!-- Generate Report -->
                    <button type="button" id="open-report-modal"
                        class="relative inline-flex items-center justify-center overflow-hidden rounded-md
                        bg-gradient-to-br from-indigo-600 to-purple-600 px-4 py-1.5 text-sm font-semibold text-white
                        shadow-md transition-all duration-300 hover:shadow-lg hover:from-indigo-700 hover:to-purple-700 group">

                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generate Report
                        </span>

                        <span
                            class="absolute inset-0 -translate-x-full bg-white/20 skew-x-12
                                transition-transform duration-700 group-hover:translate-x-full"></span>
                    </button>

                    <!-- Reset Filter -->
                    <a href="{{ route('hr.dashboard') }}"
                        class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-medium text-sm transition">
                        Reset Filter
                    </a>

                </div>
            </form>
        </div>

        <!-- Flash Messages -->
        <x-flash-message />

        {{-- <div class="flex justify-between items-center mb-4">

            <div class="flex gap-2">
                <button id="btnRequests" class="px-4 py-2 text-sm rounded bg-blue-600 text-white">
                    OT Requests
                </button>

                <button id="btnSummary" class="px-4 py-2 text-sm rounded bg-gray-200 text-gray-700">
                    OT Summary
                </button>
            </div>
        </div> --}}

        <div id="requestsTable">
            @include('hr._ot_requests_table')

            <!-- Pagination -->
            <div class="p-4">
                <x-pagination :paginator="$requests" />
            </div>
        </div>

        <div id="summaryTable" class="hidden mt-4">
        @include('hr._ot_summary_table')
        </div>

        {{-- JS for remark edit toggle --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".remark-edit-btn").forEach(btn => {
                    const parent = btn.closest(".group");
                    const display = parent.querySelector(".remark-display");
                    const form = parent.querySelector(".remark-edit-form");
                    const cancel = parent.querySelector(".remark-cancel-btn");

                    btn.addEventListener("click", () => {
                        display.classList.add("hidden");
                        form.classList.remove("hidden");
                    });

                    cancel?.addEventListener("click", () => {
                        form.classList.add("hidden");
                        display.classList.remove("hidden");
                    });
                });
            });
        </script>

        {{-- REPORT PREVIEW MODAL --}}
        <div id="report-modal"
            class="fixed inset-0 bg-black bg-opacity-60 z-[9999] hidden flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-5 flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Overtime Report Preview</h2>
                    <button onclick="closeReportModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 bg-gray-50">
                    <iframe id="report-iframe" class="w-full h-full border-0" src="" frameborder="0"></iframe>
                </div>
                <div class="bg-white border-t p-5 flex flex-wrap gap-3 justify-end">
                    <button onclick="printReport()"
                        class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        Print
                    </button>
                    <button onclick="downloadExcel()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        Excel
                    </button>
                    <button onclick="downloadPDF()"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        PDF
                    </button>
                    {{-- <button onclick="monthlyReport()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                        Monthly Report
                    </button> --}}
                </div>
            </div>
        </div>

        {{-- REPORT MODAL SCRIPT --}}
        <script>
            const modal = document.getElementById('report-modal');
            const iframe = document.getElementById('report-iframe');

            document.getElementById('open-report-modal').addEventListener('click', () => {
                const params = new URLSearchParams(window.location.search);
                iframe.src = '{{ route('hr.overtime.preview') }}?' + params.toString();
                modal.classList.remove('hidden');
            });

            function closeReportModal() {
                modal.classList.add('hidden');
                iframe.src = '';
            }

            function downloadExcel() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route('hr.overtime.export.excel') }}?' + params.toString();
            }

            function downloadPDF() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route('hr.overtime.export.pdf') }}?' + params.toString();
            }

            function printReport() {
                iframe.contentWindow.print();
            }

            function monthlyReport() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route('hr.overtime.export.excel') }}?' + params.toString();
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeReportModal();
            });

            const btnReq = document.getElementById('btnRequests');
            const btnSum = document.getElementById('btnSummary');
            const reqTable = document.getElementById('requestsTable');
            const sumTable = document.getElementById('summaryTable');

            btnReq.onclick = () => {
                reqTable.classList.remove('hidden');
                sumTable.classList.add('hidden');

                btnReq.classList.add('bg-blue-600', 'text-white');
                btnReq.classList.remove('bg-gray-200', 'text-gray-700');

                btnSum.classList.add('bg-gray-200', 'text-gray-700');
                btnSum.classList.remove('bg-blue-600', 'text-white');
            };

            btnSum.onclick = () => {
                sumTable.classList.remove('hidden');
                reqTable.classList.add('hidden');

                btnSum.classList.add('bg-blue-600', 'text-white');
                btnSum.classList.remove('bg-gray-200', 'text-gray-700');

                btnReq.classList.add('bg-gray-200', 'text-gray-700');
                btnReq.classList.remove('bg-blue-600', 'text-white');
            };
        </script>

    </div>
@endsection
