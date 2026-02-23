<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind (Filament compatible) --}}
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        {{-- Logo / Title --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">POS System</h1>
            <p class="text-sm text-gray-500 mt-1">Login to your account</p>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                >
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                >
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2 text-white font-semibold hover:bg-indigo-700 transition"
            >
                Login
            </button>
        </form>

        {{-- Footer --}}
        <div class="mt-6 text-center text-xs text-gray-400">
            © {{ date('Y') }} SiliconGlobalTech
        </div>
    </div>

</body>
</html>