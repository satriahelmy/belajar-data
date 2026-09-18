@extends('layouts.public', ['active' => 'learn'])

@section('title', $module['title'].' — BelajarData')

@section('content')
    <div class="page-shell site-container module-page">
        <a class="back-link" href="{{ route('learning.index') }}"><span aria-hidden="true">←</span> Kembali ke learning path</a>
        <header class="module-hero"><p class="eyebrow">Module {{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</p><h1>{{ $module['title'] }}</h1><p class="lede">{{ $module['purpose'] }}</p><dl class="metadata-row"><div><dt>Perkiraan waktu</dt><dd>~{{ $module['estimated_minutes'] }} menit</dd></div><div><dt>Topic</dt><dd>{{ count($module['topics']) }}</dd></div><div><dt>Status</dt><dd>Path tersedia</dd></div></dl></header>
        <div class="module-layout">
            <div>
                <section class="content-section"><p class="eyebrow">Setelah module ini</p><h2>{{ $module['outcome'] }}</h2><p class="soft-guidance">Urutan path adalah rekomendasi, bukan prerequisite lock. Module yang sudah tersedia dapat dibuka tanpa login.</p></section>
                @if ($module['recommended_knowledge'] !== [])<section class="content-section content-section--lined"><p class="eyebrow">Recommended knowledge</p><p>{{ implode(' · ', $module['recommended_knowledge']) }}</p><p class="quiet-note">Ini panduan, bukan prerequisite lock. Kamu tetap dapat membuka module yang tersedia.</p></section>@endif
                <section class="content-section content-section--lined" aria-labelledby="topics-title"><div class="section-heading-row"><div><p class="eyebrow">Di dalam module</p><h2 id="topics-title">Topics</h2></div>@if ($module['topics'] !== [])<span class="quiet-note">{{ count($module['topics']) }} topic</span>@endif</div>
                    @if ($module['topics'] === [])<div class="empty-state"><strong>Materi sedang disiapkan.</strong><p>Struktur module ini sudah menjadi bagian dari path. Lesson akan hadir setelah kontennya melewati review.</p></div>@else
                        <ol class="topic-list">@foreach ($module['topics'] as $topic)<li><span class="topic-list__number">{{ str_pad((string) $topic['order'], 2, '0', STR_PAD_LEFT) }}</span><div><a href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $module['key'], 'topicKey' => $topic['key']]) }}">{{ $topic['title'] }}</a><p>Lesson dengan content, contoh, dan practice terdaftar.</p></div><span aria-hidden="true">→</span></li>@endforeach</ol>
                    @endif
                </section>
                <section class="module-challenge" aria-labelledby="challenge-title">
                    <div>
                        <p class="module-challenge__meta">Module challenge · setelah topic sequence</p>
                        <h2 id="challenge-title">{{ $module['challenge']['title'] }}</h2>
                        <p>{{ $module['challenge']['description'] }}</p>
                        <p class="quiet-note">Challenge akan tersedia setelah practice dan assessment foundation selesai.</p>
                    </div>
                    @if ($module['topics'] !== [])
                        <a class="button button--primary" href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $module['key'], 'topicKey' => $module['topics'][0]['key']]) }}">Mulai module <span aria-hidden="true">→</span></a>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
