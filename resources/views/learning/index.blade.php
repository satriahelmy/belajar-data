@extends('layouts.public', ['active' => 'learn'])

@section('title', 'Learn | BelajarData')

@section('content')
    <div class="page-shell site-container">
        <header class="page-intro">
            <p class="eyebrow">{{ $path['title'] }}</p>
            <h1>Data Analyst Learning Path</h1>
            <p class="lede">Jalur yang direkomendasikan dari analytical thinking hingga mampu mengerjakan business analysis sendiri.</p>
            <p class="soft-guidance"><strong>Urutan ini adalah rekomendasi, bukan kunci.</strong> Kamu dapat membuka materi yang tersedia sesuai kebutuhan.</p>
            <div class="path-rail" aria-label="Urutan module 01 sampai {{ count($path['module_keys']) }}">
                <div class="path-rail__labels"><span>01</span><span>{{ str_pad((string) count($path['module_keys']), 2, '0', STR_PAD_LEFT) }}</span></div>
                <div class="path-rail__track" style="--module-count: {{ count($path['module_keys']) }}">@foreach ($path['module_keys'] as $moduleKey)<span class="path-rail__dot"><span class="sr-only">Module {{ $loop->iteration }}</span></span>@endforeach</div>
            </div>
        </header>
        <div class="curriculum-map">
            @foreach ($phases as $phase)
                <section class="curriculum-phase" aria-labelledby="phase-{{ $phase['key'] }}">
                    <div class="curriculum-phase__header"><div><p class="phase-label">Phase {{ $phase['order'] }}</p><h2 id="phase-{{ $phase['key'] }}">{{ $phase['title'] }}</h2></div><p>{{ $phase['description'] }}</p></div>
                    <div class="module-list">
                        @foreach ($phase['modules'] as $module)
                            <article id="module-{{ $module['key'] }}" class="module-row {{ $module['published'] ? 'module-row--published' : 'module-row--roadmap' }}">
                                <span class="status-dot {{ $module['published'] ? 'status-dot--available' : 'status-dot--planned' }}" aria-hidden="true"></span>
                                <div class="module-row__number">{{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="module-row__body"><div class="module-row__heading"><h3>@if ($module['published'])<a href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}">{{ $module['title'] }}</a>@else{{ $module['title'] }}@endif</h3><span class="status-label">{{ $module['published'] ? 'Tersedia' : 'Roadmap' }}</span></div><p>{{ $module['purpose'] }}</p><div class="module-row__meta"><span>{{ count($module['topics']) }} topic{{ count($module['topics']) === 1 ? '' : 's' }}</span><span>~{{ $module['estimated_minutes'] }} menit</span></div></div>
                                @if ($module['published'])<a class="module-row__action" href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}" aria-label="Buka {{ $module['title'] }}">Buka <span aria-hidden="true">→</span></a>@else<a class="module-row__action module-row__action--muted" href="#module-{{ $module['key'] }}" aria-label="Lihat posisi {{ $module['title'] }} di learning path">Lihat posisi <span aria-hidden="true">↗</span></a>@endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
