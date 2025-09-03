<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="mt-4 text-3xl font-extrabold text-gray-900">Payment Failed</h1>
        <p class="mt-2 text-base text-gray-500">Unfortunately, we were unable to process your payment. Please try again or contact support.</p>
        <div class="mt-6">
            <a href="/" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                Go back to Home<span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>
