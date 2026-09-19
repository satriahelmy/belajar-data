@extends('layouts.public', ['active' => 'learn'])

@section('title', $challengeTitle.' | BelajarData')

@section('content')
    <div class="challenge-page site-container">
        <a class="back-link" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}"><span aria-hidden="true">←</span> Kembali ke module</a>
        <header class="challenge-header">
            <p class="eyebrow">Module {{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }} · Challenge</p>
            <h1>{{ $challengeTitle }}</h1>
            <p class="lede">{{ $challenge['description'] }}</p>
            <div class="challenge-header__meta">
                <span>~{{ $challenge['estimated_minutes'] }} menit</span>
                <span>Tanpa tool khusus</span>
                <span>Evidence-first</span>
            </div>
        </header>
        <div class="challenge-layout">
            <article class="lesson-reading">
                <div class="lesson-content">{!! $lesson->html !!}</div>
                <section class="challenge-criteria" aria-labelledby="challenge-criteria-title">
                    <p class="eyebrow">Selesai jika</p>
                    <h2 id="challenge-criteria-title">Kriteria selesai</h2>
                    <ul>
                        @foreach ($challenge['completion_criteria'] as $criterion)
                            <li>{{ $criterion }}</li>
                        @endforeach
                    </ul>
                </section>
                <nav class="lesson-next-nav" aria-label="Challenge navigation">
                    <a class="lesson-next-nav__previous" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}"><span aria-hidden="true">←</span> Kembali ke module</a>
                    <a class="button button--primary" href="{{ route('learning.lesson', ['pathKey' => $pathKey, 'moduleKey' => $module['key'], 'topicKey' => $module['topics'][0]['key']]) }}">Ulangi topic pertama <span aria-hidden="true">→</span></a>
                </nav>
            </article>
        </div>
    </div>
@endsection
