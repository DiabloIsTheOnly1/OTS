<div class="bg-white shadow-xl rounded-xl overflow-hidden">
    <div class="w-full overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-blue-100 text-gray-800">
                <tr>
                    <th class="p-3 text-left w-[220px]">Employee</th>
                    <th class="p-3 text-left w-[140px]">Date</th>
                    <th class="p-3 text-center w-[140px]">Actual Hours</th>
                    <th class="p-3 text-center w-[120px]">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($otSummary as $group)
                    @php
                        $rows = $group['rows'];
                        $rowspan = $rows->count() + 1; // +1 for TOTAL row
                        $first = true;
                    @endphp

                    @foreach ($rows as $r)
                        <tr class="border-b hover:bg-gray-50">

                            {{-- EMPLOYEE (MERGED CELL) --}}
                            @if ($first)
                                <td rowspan="{{ $rowspan }}"
                                    class="p-3 font-semibold align-top bg-blue-50 border-r">
                                    {{ $group['staff']->staff_name ?? '-' }}
                                </td>
                                @php $first = false; @endphp
                            @endif

                            {{-- DATE --}}
                            <td class="p-3">
                                {{ $r->date->format('d M Y') }}
                            </td>

                            {{-- ACTUAL HOURS --}}
                            <td class="p-3 text-center font-semibold text-blue-700">
                                {{ $r->actual_hm }}
                            </td>

                            {{-- STATUS --}}
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if ($r->status === 'approved') bg-green-200 text-green-900
                                    @elseif ($r->status === 'pending') bg-yellow-200 text-yellow-900
                                    @else bg-red-200 text-red-900 @endif">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    {{-- TOTAL ROW --}}
                    <tr class="bg-blue-100 font-bold border-b">
                        <td class="p-3 text-right">Total</td>
                        <td class="p-3 text-center text-blue-900">
                            {{ $group['total_hm'] }}
                        </td>
                        <td></td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            No OT data for selected month
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
