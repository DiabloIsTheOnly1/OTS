<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container d-flex justify-content-between align-items-center">

        {{-- Left: System Name --}}
        <span class="navbar-brand mb-0 h4">
            Overtime Management System
        </span>

        {{-- Right: Login / Username --}}
        <div>
            @auth
                <span class="text-white me-3">
                    Logged in as <strong>{{ auth()->user()->username }}</strong>
                </span>

                <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">
                    Logout
                </a>
            @else
                <a href="{{ route('hr.login') }}" class="btn btn-outline-light btn-sm">
                    Login
                </a>
            @endauth
        </div>

    </div>
</nav>
