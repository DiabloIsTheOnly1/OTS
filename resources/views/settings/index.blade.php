@extends('layouts.app')

@section('content')
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-5 lg:space-y-0 px-4 lg:px-0 mx-0 lg:mx-[60px]">

        <!-- Sidebar -->
        <aside class="lg:w-64 w-full bg-white rounded-xl shadow p-5">

            <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-cog text-blue-500 mr-2"></i> Settings
            </h2>

            <nav class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-2 lg:space-y-2">

                @if (auth()->user()->canAccess('branch_settings'))
                    <a href="{{ route('settings.branch') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('settings.branch') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                        <i class="fas fa-code-branch mr-2"></i> Branch
                    </a>
                @endif

                @if (auth()->user()->canAccess('department_settings'))
                    <a href="{{ route('settings.department') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('settings.department') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                        <i class="fas fa-building mr-2"></i> Department
                    </a>
                @endif

                @if (auth()->user()->canAccess('staff_settings'))
                    <a href="{{ route('settings.staff') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('settings.staff') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                        <i class="fas fa-user-friends mr-2"></i> Staff
                    </a>
                @endif

                @if (auth()->user()->canAccess('access_level'))
                    <a href="{{ route('settings.access-level') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('settings.access-level') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                        <i class="fa-solid fa-shield-halved mr-2"></i> Access Levels
                    </a>
                @endif

                @if (auth()->user()->canAccess('user'))
                    <a href="{{ route('settings.user') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('settings.user') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                        <i class="fa-solid fa-user-gear mr-2"></i> User
                    </a>
                @endif

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 w-full">
            @yield('settings-content')
        </main>

    </div>
@endsection
