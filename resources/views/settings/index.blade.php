@extends('layouts.app')

@section('content')

<div class="flex flex-col lg:flex-row lg:space-x-6 space-y-5 lg:space-y-0 px-4 lg:px-0">

    <!-- Sidebar -->
    <aside class="lg:w-64 w-full bg-white rounded-xl shadow p-5">

        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-cog text-blue-500 mr-2"></i> Settings
        </h2>

        <nav class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-2 lg:space-y-2">

            <a href="{{ route('settings.branch') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition
               {{ request()->routeIs('settings.branch') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-code-branch mr-2"></i> Branch
            </a>

            <a href="{{ route('settings.department') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition
               {{ request()->routeIs('settings.department') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-building mr-2"></i> Department
            </a>

            <a href="{{ route('settings.user') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition
               {{ request()->routeIs('settings.user') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-users mr-2"></i> User
            </a>

        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white rounded-xl shadow p-5 w-full">
        @yield('settings-content')
    </main>

</div>

@endsection
