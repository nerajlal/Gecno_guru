<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GecnoGuru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a, #6d28d9);
        }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <a href="/" class="text-3xl font-bold text-white">
                    <span class="text-blue-300">Gecno</span>Guru
                </a>
                <h2 class="text-2xl font-bold text-white mt-4">Login to Your Account</h2>
            </div>

            @if ($errors->any())
                <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="login-email" class="block text-white mb-2">Email</label>
                    <input type="email" id="login-email" name="email" class="w-full bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg py-2 px-4 text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="your@email.com" required value="{{ old('email') }}">
                </div>
                <div class="mb-6">
                    <label for="login-password" class="block text-white mb-2">Password</label>
                    <input type="password" id="login-password" name="password" class="w-full bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg py-2 px-4 text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="********" required>
                </div>
                <button type="submit" class="w-full bg-white text-blue-700 font-bold py-3 rounded-full hover:bg-blue-100 transition-all duration-200">Login</button>
            </form>
            <p class="text-center text-white mt-6">
                Don't have an account? <a href="/#register" class="text-blue-300 hover:underline font-semibold">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>
