@extends('layouts.public', ['active' => 'projects'])

@section('title', 'Reference Approach · '.$project['title'].' | BelajarData')

@section('content')
    <div class="page-shell site-container project-page">
        <a class="back-link" href="{{ route('projects.show', ['projectKey' => $project['key']]) }}"><span aria-hidden="true">←</span> Kembali ke project</a>
        <header class="project-stage-header">
            <p class="eyebrow">Reference Approach</p>
            <h1>{{ $project['title'] }}</h1>
            <p class="lede">Satu pendekatan yang defensible, bukan satu-satunya jawaban benar.</p>
        </header>

        @if (! $unlocked)
            <section class="project-reference-lock">
                <p class="eyebrow">Coba dulu</p>
                <h2>Sudah punya rencana sendiri?</h2>
                <p>Reference Approach paling berguna setelah kamu mencoba brief dan menyusun bukti sendiri. Kamu dapat membukanya sekarang jika ingin membandingkan arah analisis.</p>
                <form method="POST" action="{{ route('projects.reference.unlock', ['projectKey' => $project['key']]) }}">
                    @csrf
                    <button class="button button--primary" type="submit">Buka Reference Approach <span aria-hidden="true">→</span></button>
                </form>
            </section>
        @else
            <article class="lesson-reading project-reading">
                <div class="lesson-content">{!! $lesson->html !!}</div>
            </article>
        @endif
    </div>
@endsection
