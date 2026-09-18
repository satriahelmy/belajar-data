@extends('layouts.public', ['active' => 'learn'])

@section('title', $lessonTitle.' — BelajarData')

@section('content')
    <div class="lesson-page site-container">
        <a class="back-link" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}"><span aria-hidden="true">←</span> {{ $module['title'] }}</a>
        <div class="lesson-context"><p class="eyebrow">Module {{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }} · Topic {{ $navigation['current']['order'] }} dari {{ $navigation['total'] }}</p><p class="lesson-context__title">{{ $module['title'] }}</p></div>
        <div class="lesson-workspace">
            <details class="lesson-mobile-nav"><summary>Topic navigation <span aria-hidden="true">⌄</span></summary>@include('partials.lesson-nav', ['module' => $module, 'navigation' => $navigation, 'mobile' => true])</details>
            <aside class="lesson-sidebar" aria-label="Module topic navigation">@include('partials.lesson-nav', ['module' => $module, 'navigation' => $navigation, 'mobile' => false])</aside>
            <article class="lesson-reading">
                <div class="lesson-reading__header"><p class="eyebrow">Lesson</p><h1>{{ $lessonTitle }}</h1><p class="quiet-note">{{ $module['purpose'] }}</p></div>
                @if (count($lesson->headings) > 1)<details class="lesson-outline" open><summary>On this page</summary><ol>@foreach ($lesson->headings as $heading)<li data-heading-level="{{ $heading['level'] }}"><a href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a></li>@endforeach</ol></details>@endif
                <div class="lesson-content">{!! $lesson->html !!}</div>
                @if (($topic['further_reading'] ?? []) !== [])<section class="further-reading" aria-labelledby="further-reading-title"><p class="eyebrow">Further Reading</p><h2 id="further-reading-title">Lanjutkan bila ingin memperdalam</h2>@foreach ($topic['further_reading'] as $resource)<div class="further-reading__item"><a href="{{ $resource['url'] }}">{{ $resource['title'] }}</a><p>{{ $resource['reason'] }}</p></div>@endforeach</section>@endif
                <nav class="lesson-next-nav" aria-label="Lesson navigation">
                    @if ($navigation['previous'])<a class="lesson-next-nav__previous" href="{{ route('learning.lesson', ['pathKey' => $pathKey, 'moduleKey' => $module['key'], 'topicKey' => $navigation['previous']['key']]) }}"><span aria-hidden="true">←</span> {{ $navigation['previous']['title'] }}</a>@else<span></span>@endif
                    @if ($navigation['next'])<a class="button button--primary" href="{{ route('learning.lesson', ['pathKey' => $pathKey, 'moduleKey' => $module['key'], 'topicKey' => $navigation['next']['key']]) }}">Lanjut ke topic berikutnya <span aria-hidden="true">→</span></a>@else<a class="button button--primary" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}">Kembali ke module <span aria-hidden="true">→</span></a>@endif
                </nav>
            </article>
        </div>
    </div>
@endsection
