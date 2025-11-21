<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Overtime Management System' }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    {{-- Topbar --}}
    @include('layouts.topbar')

    {{-- Page Content --}}
    <div class="container mx-auto py-6 px-4">
        @yield('content')
    </div>

</body>
</html>
