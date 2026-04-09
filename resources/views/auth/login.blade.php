<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm bg-white border border-gray-200 p-8">

        <h1 class="text-sm font-bold uppercase tracking-widest mb-8">Login</h1>

        @if ($errors->any())
            <p class="text-xs text-red-600 border border-red-200 px-3 py-2 mb-4">
                {{ $errors->first('msg') }}
            </p>
        @endif

        <form method="POST" action="/login" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       class="w-full border border-black px-3 py-2 text-sm focus:outline-none">
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-black px-3 py-2 text-sm focus:outline-none">
            </div>

            <button type="submit"
                    class="bg-black text-white px-4 py-2 text-xs uppercase tracking-widest hover:opacity-80 transition mt-2">
                Masuk
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Kredensial</p>
            <p class="text-xs text-gray-500">Username: <span class="font-medium text-black">admin</span></p>
            <p class="text-xs text-gray-500">Password: <span class="font-medium text-black">admin123</span></p>
        </div>

    </div>

</body>
</html>
