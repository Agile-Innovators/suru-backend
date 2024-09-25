<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Suru Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#EFF0F8]">
    @include('partials.nav')
    <section class="max-w-[90%] mx-auto">
        @yield('content')
    </section>
</body>
</html>