@extends('layouts.app')

@section('content')
<div class="flex space-x-6">

    <!-- Sidebar -->
    <aside class="w-64 bg-white rounded-xl shadow p-5 h-fit">

        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-cog text-blue-500 mr-2"></i> Settings
        </h2>

        <nav class="space-y-2">

            <a href="{{ route('settings.branch') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium 
               {{ request()->routeIs('settings.branch') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-code-branch mr-2"></i> Branch Management
            </a>

            <a href="{{ route('settings.department') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('settings.department') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-building mr-2"></i> Department Management
            </a>

            <a href="{{ route('settings.user') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('settings.user') ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                <i class="fas fa-users mr-2"></i> User Management
            </a>

        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('settings-content')
    </main>

</div>
@endsection
