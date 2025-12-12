<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Overtime Management System' }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body class="bg-gray-100">

    {{-- Topbar --}}
    @include('layouts.topbar')

    {{-- Page Content --}}
    <div class="container-fluid mx-auto py-4 px-4 sm:px-8 lg:px-10">
        @yield('content')
    </div>

</body>
</html>
