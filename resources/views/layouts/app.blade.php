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

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (required by Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Select2 Tailwind-like styling */
        .select2-container--default .select2-selection--single {
            height: 44px;
            /* matches py-3 input height */
            border: 1px solid #d1d5db;
            /* border-gray-300 */
            border-radius: 0.5rem;
            /* rounded-lg */
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            padding-right: 0;
            color: #111827;
            /* text-gray-900 */
            line-height: normal;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6;
            /* blue-500 */
            box-shadow: 0 0 0 2px rgb(59 130 246 / 40%);
            /* focus:ring-2 */
        }

        /* Dropdown */
        .select2-dropdown {
            border-radius: 0.5rem;
            border-color: #d1d5db;
        }

        .select2-results__option {
            padding: 0.5rem 0.75rem;
        }

        .select2-results__option--highlighted {
            background-color: #3b82f6 !important;
            color: white;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            min-height: 38px;
            padding: 2px 6px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, .2);
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection__choice {
            display: none !important;
        }

        .select2-selection__rendered {
            display: flex !important;
            align-items: center;
        }

        .select2-selection__placeholder {
            margin-left: 6px;
        }
    </style>

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
