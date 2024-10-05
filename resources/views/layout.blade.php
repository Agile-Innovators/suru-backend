<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Suru Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="flex flex-row font-main bg-[#EFF0F8]">
    <div
        class="fixed flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 h-[100vh] w-full max-w-[20rem] p-4 shadow-xl shadow-blue-gray-900/5">
        <div class="flex justify-center items-center p-4">
            <img class="m-4 h-8" src="{{ asset('icons/logo.svg') }}" alt="Logo">
        </div>
        @include('partials.nav')
    </div>
    <div class="w-full ml-[20rem] p-5">
        @yield('content')
        <h1>Hola c:</h1>
    </div>
</body>
</html>