<div x-data="{
    openPartial: false,
    hm: '{{ $actualHm }}',
    toMinutes(hm) {
        let [h, m] = hm.split(':').map(Number);
        return (h * 60) + m;
    }
}">

    {{-- Trigger button --}}
    <button @click="openPartial = true"
        type="button"
        class="w-full md:w-auto
            px-4 
           md:py-1 py-1      
           text-xs font-medium rounded transition-colors text-center
           w-full            
           md:w-auto        
        {{-- {{ $canApprove ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}" --}}
        @if ($canApprove) bg-blue-600 hover:bg-blue-700 text-white
        @elseif (!$canHod && !$canHq) bg-gray-300 text-gray-500 cursor-not-allowed
        @else bg-gray-800 text-gray-400 cursor-not-allowed @endif"
        {{ $canApprove ? '' : 'disabled' }}>
        Con. Approve
    </button>

    {{-- Modal Background --}}
    <div x-show="openPartial" @click="openPartial = false" class="fixed inset-0 bg-black bg-opacity-40 z-40"
        x-transition.opacity>
    </div>

    {{-- Modal --}}
    <div x-show="openPartial"
        class="fixed top-1/2 left-1/2 w-80 -translate-x-1/2 -translate-y-1/2
            bg-white p-5 rounded-xl shadow-xl z-50"
        x-transition @click.away="openPartial = false">

        <h2 class="text-lg font-bold mb-2">Approve Partial Hours</h2>

        <div class="text-center space-y-1 mb-3 mx-12">
            <p class="text-xs bg-blue-100 text-blue-800 rounded-md p-1">
                Actual Hours: <strong>{{ $actualHm }}</strong>
            </p>

            <p class="text-xs bg-amber-100 text-amber-800 rounded-md p-1">
                Requested Hours: <strong>{{ $requestedHm }}</strong>
            </p>
        </div>

        <form action="{{ route('hr.overtime.approvePartial', $id) }}" method="POST">
            @csrf

            <label class="text-sm font-medium">Approve (HH:MM)</label>

            <input type="text" x-model="hm" class="border w-full px-2 py-1 rounded mt-1 text-sm"
                placeholder="HH:MM">
                {{-- @input="
                let max = {{ $actualMinutes }};
                let v = toMinutes(hm);
                if (v > max) {
                    let h = Math.floor(max / 60);
                    let m = max % 60;
                    hm = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                }
            "> --}}

            <input type="hidden" name="approved_minutes" :value="toMinutes(hm)">

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="openPartial = false" class="px-3 py-1 text-xs bg-gray-300 rounded">
                    Cancel
                </button>

                <button type="submit" class="px-3 py-1 text-xs bg-blue-600 text-white rounded">
                    Approve
                </button>
            </div>
        </form>
    </div>

</div>
