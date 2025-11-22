<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen text-gray-800">
    <div class="text-center p-6 bg-white rounded shadow-lg max-w-md">
        <div class="text-6xl text-indigo-600 font-bold mb-4" x-text="@yield('code')"></div>
        <h1 class="text-2xl font-bold mb-2" id="error-message">@yield('message')</h1>
        <p class="text-gray-600 mb-6">@yield('description')</p>
        @yield('action')
    </div>
</body>
</html>
