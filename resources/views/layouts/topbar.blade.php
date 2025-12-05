<nav x-data="{ open: false }"
     class="bg-gradient-to-r from-gray-900 to-blue-900 text-white shadow-lg">
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- MAIN NAVBAR -->
        <div class="flex justify-between items-center py-3">

            {{-- OT Logo --}}
            {{-- <a href="{{ route('overtime.index') }}" class="flex items-center group cursor-pointer"> --}}
                <div class="flex-shrink-0">
                    <div class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent
                                group-hover:opacity-80 transition duration-200">
                        OT
                    </div>
                </div>
            </a>

            {{-- Right Section (Desktop View) --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    {{-- All desktop buttons go here (Your original desktop content) --}}
                    <a href="{{ route('hr.dashboard') }}" class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 min-w-max">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                        <span class="text-sm font-medium">Request List</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 min-w-max">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="text-sm font-medium">Settings</span>
                    </a>
                    <div class="flex items-center space-x-3 bg-gray-800 px-4 py-2 rounded-lg border border-gray-700 min-w-max">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-gray-300 text-sm">Logged in as <strong class="text-white font-semibold">{{ auth()->user()->username }}</strong></span>
                    </div>
                    <a href="{{ route('logout') }}" class="flex items-center space-x-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 min-w-max">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span class="text-sm font-medium">Logout</span>
                    </a>

                @else
                    {{-- Login Button (Desktop) --}}
                    <a href="{{ route('login') }}" class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 min-w-max">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        <span class="text-sm font-medium">Login</span>
                    </a>
                @endauth
            </div>

            {{-- 📱 Mobile Menu (NO JS) --}}
            <details class="md:hidden">
                <summary class="list-none focus:outline-none cursor-pointer">
                    {{-- Hamburger Icon --}}
                    <div class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white p-2 rounded-md">
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                </summary>

                {{-- Mobile Menu Flyout Content --}}
                <div class="absolute right-0 top-16 w-full bg-gray-800 border-t border-gray-700 py-3 shadow-2xl">
                    <div class="px-4 space-y-2 flex flex-col items-start">
                        @auth
                            {{-- Request List Button (Mobile) --}}
                            <a href="{{ route('hr.dashboard') }}" class="w-full text-left flex items-center space-x-3 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-150 ease-in-out">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                <span class="text-base font-medium">Request List</span>
                            </a>
                            {{-- Settings Button (Mobile) --}}
                            <a href="{{ route('settings.index') }}" class="w-full text-left flex items-center space-x-3 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-150 ease-in-out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span class="text-base font-medium">Settings</span>
                            </a>
                            {{-- Logged in Badge (Mobile) --}}
                            <div class="w-full flex items-center space-x-3 bg-gray-700 px-3 py-2 rounded-md border border-gray-600 min-w-max">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-gray-300 text-base">Logged in as <strong class="text-white font-semibold">{{ auth()->user()->username }}</strong></span>
                            </div>
                            {{-- Logout Button (Mobile) --}}
                            <a href="{{ route('logout') }}" class="w-full text-left flex items-center space-x-3 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition duration-150 ease-in-out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                <span class="text-base font-medium">Logout</span>
                            </a>
                        @else
                            {{-- Login Button (Mobile) --}}
                            <a href="{{ route('login') }}" class="w-full text-left flex items-center space-x-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-3 py-2 rounded-md transition duration-150 ease-in-out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                <span class="text-base font-medium">Login</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </details>
        </div>

        <!-- MOBILE DROPDOWN MENU -->
        <div x-show="open" x-transition.origin.top class="sm:hidden bg-gray-800 rounded-lg p-3 space-y-2">

            @auth
                <a href="{{ route('hr.dashboard') }}"
                    class="block bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-md text-sm">Request List</a>

                <a href="{{ route('settings.index') }}"
                    class="block bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-md text-sm">Settings</a>

                <a href="{{ route('logout') }}"
                    class="block bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm">Logout</a>

                <div class="bg-gray-700 px-4 py-2 rounded-md text-sm">
                    Logged in as <strong>{{ auth()->user()->username }}</strong>
                </div>

            @else
                <a href="{{ route('login') }}"
                    class="block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-sm">Login</a>
            @endauth

        </div>

    </div>
</nav>