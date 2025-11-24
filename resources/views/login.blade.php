<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-indigo-700">

    <div class="w-full max-w-sm bg-white/90 backdrop-blur-md rounded-2xl shadow-xl p-8 animate-fadeIn">

        <h2 class="text-3xl font-bold text-center mb-6 text-indigo-800">
            Admin Login
        </h2>

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 text-sm px-4 py-2 rounded-lg text-center">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                    placeholder="Enter username"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                    placeholder="Enter password"
                    required
                >
            </div>

            <button
                class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-semibold py-2.5 rounded-xl shadow-md transition duration-200"
            >
                Login
            </button>

        </form>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out;
        }
    </style>

</body>
</html>
