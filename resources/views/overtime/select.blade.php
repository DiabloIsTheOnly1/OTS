<!-- resources/views/overtime/select.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Branch & Department</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    @extends('layouts.app')
    @section('content')

    <div class="flex items-center justify-center mt-8">
        <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                Select Branch & Department
            </h2>

            <form method="POST" action="{{ route('overtime.setFilters') }}" class="space-y-4">
                @csrf

                <!-- Branch -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg mt-4">
                    Continue
                </button>
            </form>
        </div>
    </div>
    @endsection
</body>

</html>
