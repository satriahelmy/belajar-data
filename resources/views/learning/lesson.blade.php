<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $lessonTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="learning-lesson" data-content-key="{{ $lessonKey }}">
        <p class="learning-lesson__eyebrow">{{ $pathKey }} / {{ $lessonKey }}</p>

        <aside class="learning-lesson__outline" aria-label="On this page">
            <strong>On this page</strong>
            <ol>
                @foreach ($lesson->headings as $heading)
                    <li data-heading-level="{{ $heading['level'] }}">
                        <a href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a>
                    </li>
                @endforeach
            </ol>
        </aside>

        <article class="learning-lesson__content">
            {!! $lesson->html !!}
        </article>
    </main>
</body>
</html>
