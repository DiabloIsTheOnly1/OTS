<div class="bg-white rounded-lg border border-gray-200 overflow-hidden w-full md:w-full lg:w-2/3">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="py-2.5 px-3 text-left font-medium lg:w-[250px]">Employee</th>
                    <th class="py-2.5 px-3 text-left font-medium">Date</th>
                    <th class="py-2.5 px-3 text-center font-medium">Hours</th>
                    <th class="py-2.5 px-3 text-center font-medium">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($otSummary as $group)
                    @php
                        $rows = $group['rows'];
                        $rowspan = $rows->count() + 1;
                        $first = true;
                    @endphp

                    @foreach ($rows as $r)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            @if ($first)
                                <td rowspan="{{ $rowspan }}" class="p-3 align-top border-r bg-gray-50/30 border-b border-gray-100">
                                    <div class="space-y-1">
                                        <div class="font-medium text-gray-900">{{ $group['staff']->staff_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $group['staff']->branch->name }} • {{ $group['staff']->position ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $group['staff']->department->department_name }}</div>
                                    </div>
                                </td>
                                @php $first = false; @endphp
                            @endif

                            <td class="p-3">
                                <div class="text-gray-700">{{ $r->date->format('d M') }}</div>
                                {{-- <div class="text-xs text-gray-400">{{ $r->date->format('D') }}</div> --}}
                            </td>

                            <td class="p-3 text-center">
                                <span class="font-semibold text-blue-700">
                                    {{ $r->actual_hm }}
                                </span>
                            </td>

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

                    <tr class="bg-blue-50">
                        <td class="p-1 text-right font-medium text-gray-700">Total</td>
                        <td class="p-1 text-center font-bold text-blue-800">{{ $group['total_hm'] }}</td>
                        <td class="p-1"></td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">
                            <div class="space-y-2">
                                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm">No overtime records</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>