@extends('layouts.public', ['active' => 'projects'])

@section('title', $project['title'].' | Projects | BelajarData')

@section('content')
    <div class="page-shell site-container project-page">
        <a class="back-link" href="{{ route('projects.index') }}"><span aria-hidden="true">←</span> Kembali ke projects</a>
        <header class="project-header">
            <p class="eyebrow">Project {{ str_pad((string) $project['number'], 2, '0', STR_PAD_LEFT) }} · {{ $project['difficulty'] }}</p>
            <h1>{{ $project['title'] }}</h1>
            <p class="lede">{{ $project['description'] }}</p>
            <div class="project-header__meta">
                <span>{{ $project['focus'] }}</span>
                <span>~{{ $project['estimated_minutes'] }} menit</span>
                <span>Tool choice terbuka</span>
            </div>
        </header>

        <div class="project-layout">
            <main>
                <section class="project-brief" aria-labelledby="project-brief-title">
                    <p class="eyebrow">The Brief</p>
                    <h2 id="project-brief-title">Mulai dari masalah bisnis, bukan urutan command.</h2>
                    <p>{{ $project['description'] }}</p>
                    <ul>
                        @foreach ($project['outcomes'] as $outcome)
                            <li>{{ $outcome }}</li>
                        @endforeach
                    </ul>
                </section>

                <section class="project-stage-list" aria-labelledby="project-stages-title">
                    <div class="section-heading-row">
                        <h2 id="project-stages-title">Enam stage kerja</h2>
                        @if ($progress)
                            <span class="status-label">{{ $progress->status === 'completed' ? 'Selesai' : 'Sedang berjalan' }}</span>
                        @endif
                    </div>
                    @foreach ($project['stage_keys'] as $stageKey)
                        @php($stage = $project['stages'][$stageKey])
                        <a class="project-stage-row" href="{{ route('projects.stage', ['projectKey' => $project['key'], 'stageKey' => $stageKey]) }}">
                            <span>{{ str_pad((string) $stage['number'], 2, '0', STR_PAD_LEFT) }}</span>
                            <strong>{{ $stage['title'] }}</strong>
                            <span class="project-stage-row__status">{{ ($statuses[$stageKey] ?? false) ? 'Selesai' : 'Buka' }} <span aria-hidden="true">→</span></span>
                        </a>
                    @endforeach
                </section>

                <section class="project-tools" aria-labelledby="project-tools-title">
                    <p class="eyebrow">Tool guidance</p>
                    <h2 id="project-tools-title">Pilih workflow yang bisa kamu pertanggungjawabkan</h2>
                    <ul>
                        @foreach ($project['tool_guidance'] as $guidance)
                            <li>{{ $guidance }}</li>
                        @endforeach
                    </ul>
                </section>
            </main>

            <aside class="project-sidebar">
                <p class="eyebrow">Progress</p>
                <strong>{{ collect($statuses)->filter()->count() }} / {{ count($project['stage_keys']) }} stage selesai</strong>
                @auth
                    <form method="POST" action="{{ route('projects.start', ['projectKey' => $project['key']]) }}">
                        @csrf
                        <button class="button button--primary" type="submit">{{ $progress ? 'Lanjutkan project' : 'Mulai project' }} <span aria-hidden="true">→</span></button>
                    </form>
                @else
                    <p>Project dapat dibaca tanpa login. Login diperlukan untuk menyimpan stage dan jawaban.</p>
                    <a class="button button--primary" href="{{ route('login') }}">Login untuk menyimpan <span aria-hidden="true">→</span></a>
                @endauth
                <a class="text-link" href="{{ route('projects.reference', ['projectKey' => $project['key']]) }}">Reference Approach</a>
            </aside>
        </div>
    </div>
@endsection
