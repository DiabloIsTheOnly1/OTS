@props(['paginator'])

@if ($paginator->hasPages())
<div class="flex items-center justify-between border-t border-gray-200  px-4 py-3 sm:px-6">

    {{-- Mobile Previous / Next --}}
    <div class="flex flex-1 justify-between sm:hidden">
        <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
           class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium
                {{ $paginator->onFirstPage() ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50' }}">
            Previous
        </a>

        <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
           class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium
                {{ !$paginator->hasMorePages() ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50' }}">
            Next
        </a>
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">

        {{-- Showing info + Page size selector --}}
        <div class="flex items-center gap-4">

            {{-- Showing items --}}
            <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-medium">{{ $paginator->total() }}</span>
                results
            </p>

            {{-- Page size selector --}}
            <form method="GET" class="inline-flex items-center gap-2">
                {{-- Keep existing filters --}}
                @foreach(request()->except(['page', 'per_page']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <select name="per_page" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                    @foreach([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>
                            Show {{ $size }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Pagination links --}}
        <div class="bg-white rounded-lg shadow-md">
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-xs">

                {{-- Previous page --}}
                <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
                   class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 border border-gray-200
                          {{ $paginator->onFirstPage() ? 'cursor-not-allowed' : 'hover:bg-gray-100' }}">
                    <span class="sr-only">Previous</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"/>
                    </svg>
                </a>

                {{-- Page numbers --}}
                @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                           class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-100 border border-gray-200">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Next page --}}
                <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
                   class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 border border-gray-200
                          {{ !$paginator->hasMorePages() ? 'cursor-not-allowed' : 'hover:bg-gray-50' }}">
                    <span class="sr-only">Next</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"/>
                    </svg>
                </a>

            </nav>
        </div>

    </div>
</div>
@endif
