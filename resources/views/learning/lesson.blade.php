@extends('layouts.public', ['active' => 'learn'])

@section('title', $lessonTitle.' | BelajarData')

@section('content')
    <div class="lesson-page site-container">
        <a class="back-link" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}"><span aria-hidden="true">←</span> {{ $module['title'] }}</a>
        <div class="lesson-context"><p class="eyebrow">Module {{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }} · Topic {{ $navigation['current']['order'] }} dari {{ $navigation['total'] }}</p><p class="lesson-context__title">{{ $module['title'] }}</p></div>
        <details class="lesson-mobile-nav"><summary>Di halaman ini <span aria-hidden="true">⌄</span></summary>@include('partials.lesson-nav', ['navigation' => $navigation, 'lesson' => $lesson, 'mobile' => true])</details>
        <div class="lesson-workspace">
            <article class="lesson-reading">
                <header class="lesson-reading__header"><div class="lesson-reading__meta"><p class="eyebrow">Lesson</p></div><h1>{{ $lessonTitle }}</h1><p class="quiet-note">{{ $module['purpose'] }}</p><div class="lesson-progress" data-topic-progress data-progress-key="{{ $progressKey }}" data-progress-state="{{ $progressState ?? 'not_started' }}"><span class="lesson-progress__status" data-progress-status>{{ $progressState === 'completed' ? 'Topic selesai' : 'Belum dimulai' }}</span><button class="button button--quiet" type="button" data-progress-action>{{ $progressState === 'completed' ? 'Topic selesai' : 'Tandai selesai' }}</button></div></header>
                <div class="lesson-content">{!! $lesson->html !!}</div>
                @if (($topic['further_reading'] ?? []) !== [])<section class="further-reading" aria-labelledby="further-reading-title"><p class="eyebrow">Further Reading</p><h2 id="further-reading-title">Lanjutkan bila ingin memperdalam</h2>@foreach ($topic['further_reading'] as $resource)<div class="further-reading__item"><a href="{{ $resource['url'] }}">{{ $resource['title'] }}</a><p>{{ $resource['reason'] }}</p></div>@endforeach</section>@endif
                <nav class="lesson-next-nav" aria-label="Lesson navigation">
                    @if ($navigation['previous'])<a class="lesson-next-nav__previous" href="{{ route('learning.lesson', ['pathKey' => $pathKey, 'moduleKey' => $module['key'], 'topicKey' => $navigation['previous']['key']]) }}"><span aria-hidden="true">←</span> {{ $navigation['previous']['title'] }}</a>@else<span></span>@endif
                    @if ($navigation['next'])<a class="button button--primary" href="{{ route('learning.lesson', ['pathKey' => $pathKey, 'moduleKey' => $module['key'], 'topicKey' => $navigation['next']['key']]) }}">Lanjut ke topic berikutnya <span aria-hidden="true">→</span></a>@else<a class="button button--primary" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}">Kembali ke module <span aria-hidden="true">→</span></a>@endif
                </nav>
            </article>
            <aside class="lesson-sidebar" aria-label="Lesson section navigation">@include('partials.lesson-nav', ['navigation' => $navigation, 'lesson' => $lesson, 'mobile' => false])</aside>
        </div>
    </div>
@endsection
