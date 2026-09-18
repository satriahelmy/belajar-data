<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BelajarData — jalur belajar Data Analyst yang terstruktur.">
    <title>@yield('title', 'BelajarData')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body">
    @include('partials.header', ['active' => $active ?? null])
    <main>@yield('content')</main>
    @include('partials.footer')
</body>
</html>
