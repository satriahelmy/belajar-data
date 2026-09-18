@extends('layouts.public', ['active' => 'learn'])

@section('title', 'Learn — BelajarData')

@section('content')
    <div class="page-shell site-container">
        <header class="page-intro"><p class="eyebrow">{{ $path['title'] }}</p><h1>Data Analyst Learning Path</h1><p class="lede">Jalur yang direkomendasikan dari analytical thinking hingga mampu mengerjakan business analysis sendiri.</p><p class="soft-guidance"><strong>Urutan ini adalah rekomendasi, bukan kunci.</strong> Kamu dapat membuka materi yang tersedia sesuai kebutuhan.</p></header>
        <div class="curriculum-map">
            @foreach ($phases as $phase)
                <section class="curriculum-phase" aria-labelledby="phase-{{ $phase['key'] }}">
                    <div class="curriculum-phase__header"><div><p class="phase-label">Phase {{ $phase['order'] }}</p><h2 id="phase-{{ $phase['key'] }}">{{ $phase['title'] }}</h2></div><p>{{ $phase['description'] }}</p></div>
                    <div class="module-list">
                        @foreach ($phase['modules'] as $module)
                            <article id="module-{{ $module['key'] }}" class="module-row {{ $module['published'] ? 'module-row--published' : 'module-row--roadmap' }}">
                                <div class="module-row__number">{{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="module-row__body"><div class="module-row__heading"><h3>@if ($module['published'])<a href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}">{{ $module['title'] }}</a>@else{{ $module['title'] }}@endif</h3><span class="status-label">{{ $module['published'] ? 'Tersedia' : 'Roadmap' }}</span></div><p>{{ $module['purpose'] }}</p><div class="module-row__meta"><span>{{ count($module['topics']) }} topic{{ count($module['topics']) === 1 ? '' : 's' }}</span><span>~{{ $module['estimated_minutes'] }} menit</span></div></div>
                                @if ($module['published'])<a class="module-row__action" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}" aria-label="Buka {{ $module['title'] }}">Buka <span aria-hidden="true">→</span></a>@else<span class="module-row__action module-row__action--muted">Segera</span>@endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
