<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $application }}: foundation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <h1>{{ $application }}</h1>
        <p>Laravel foundation is ready for M0A.</p>
        <dl>
            <dt>Laravel</dt>
            <dd>{{ $laravel }}</dd>
            <dt>Environment</dt>
            <dd>{{ app()->environment() }}</dd>
        </dl>
    </main>
</body>
</html>
