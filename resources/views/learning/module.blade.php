@extends('layouts.public', ['active' => 'learn'])

@section('title', $module['title'].' — BelajarData')

@section('content')
    <div class="page-shell site-container module-page">
        <a class="back-link" href="{{ route('learning.index') }}"><span aria-hidden="true">←</span> Kembali ke learning path</a>
        <header class="module-hero">
            <div class="module-hero__identity">
                <p class="module-hero__kicker"><span class="module-hero__number">{{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</span><span>{{ $phase['title'] ?? 'Data Analyst Path' }}</span></p>
                <h1>{{ $module['title'] }}</h1>
            </div>
            <p class="lede">{{ $module['purpose'] }}</p>
            <div class="module-hero__details">
                <dl class="metadata-row"><div><dt>Perkiraan waktu</dt><dd>~{{ $module['estimated_minutes'] }} menit</dd></div><div><dt>Topic</dt><dd>{{ count($module['topics']) }}</dd></div><div><dt>Status</dt><dd>Path tersedia</dd></div></dl>
                @if ($module['topics'] !== [])<a class="button button--primary module-hero__action" href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $module['key'], 'topicKey' => $module['topics'][0]['key']]) }}">Mulai belajar <span aria-hidden="true">→</span></a>@endif
            </div>
        </header>
        <div class="module-layout">
            <div>
                <section class="content-section content-section--outcome"><div class="module-outcome"><div><p class="eyebrow">Setelah module ini</p><h2>{{ $module['outcome'] }}</h2></div><p class="path-note"><span class="status-dot status-dot--available" aria-hidden="true"></span><span>Urutan path adalah rekomendasi, bukan prerequisite lock. Module yang sudah tersedia dapat dibuka tanpa login.</span></p></div></section>
                @if ($module['recommended_knowledge'] !== [])<section class="content-section content-section--lined"><p class="eyebrow">Recommended knowledge</p><p>{{ implode(' · ', $module['recommended_knowledge']) }}</p><p class="quiet-note">Ini panduan, bukan prerequisite lock. Kamu tetap dapat membuka module yang tersedia.</p></section>@endif
                <section class="content-section content-section--lined" aria-labelledby="topics-title"><div class="section-heading-row"><div><p class="eyebrow">Di dalam module</p><h2 id="topics-title">Topics</h2></div>@if ($module['topics'] !== [])<span class="quiet-note">{{ count($module['topics']) }} {{ count($module['topics']) === 1 ? 'topic' : 'topics' }}</span>@endif</div>
                    @if ($module['topics'] === [])<div class="empty-state"><strong>Materi sedang disiapkan.</strong><p>Struktur module ini sudah menjadi bagian dari path. Lesson akan hadir setelah kontennya melewati review.</p></div>@else
                        <ol class="topic-list">@foreach ($module['topics'] as $topic)<li><span class="topic-list__marker status-dot status-dot--available" aria-hidden="true"></span><span class="topic-list__number">{{ str_pad((string) $topic['order'], 2, '0', STR_PAD_LEFT) }}</span><div><a href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $module['key'], 'topicKey' => $topic['key']]) }}">{{ $topic['title'] }}</a><p>Lesson dengan content, contoh, dan practice terdaftar.</p></div><span aria-hidden="true">→</span></li>@endforeach</ol>
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
                        <a class="button button--primary" href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $module['key'], 'topicKey' => $module['topics'][0]['key']]) }}">Mulai belajar <span aria-hidden="true">→</span></a>
                    @endif
                </section>
                @if ($nextModule)<section class="module-next"><div><p class="eyebrow">Module berikutnya</p><h2>{{ str_pad((string) $nextModule['number'], 2, '0', STR_PAD_LEFT) }} — {{ $nextModule['title'] }}</h2><p>{{ $nextModule['purpose'] }}</p></div><a class="text-link" href="{{ route('learning.index') }}#module-{{ $nextModule['key'] }}">Lihat posisinya di path <span aria-hidden="true">→</span></a></section>@endif
            </div>
        </div>
    </div>
@endsection
