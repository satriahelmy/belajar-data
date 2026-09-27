@extends('layouts.public', ['active' => 'projects'])

@section('title', 'Projects | BelajarData')

@section('content')
    <div class="page-shell site-container projects-page">
        <header class="page-intro">
            <p class="eyebrow">Projects</p>
            <h1>Tempat skill analisis bertemu kasus bisnis.</h1>
            <p class="lede">Pilih kasus, susun bukti, lalu jelaskan apa yang didukung data dan apa yang masih perlu dicari.</p>
        </header>

        <section class="project-list" aria-labelledby="project-list-title">
            <div class="section-heading-row">
                <h2 id="project-list-title">Project library</h2>
                <span class="status-label">{{ count(array_filter($projects, fn (array $item): bool => $item['project']['status'] === 'published')) }} launch projects</span>
            </div>

            @foreach ($projects as $item)
                @php($project = $item['project'])
                @php($progress = $item['progress'])
                <article class="project-list__item {{ $project['status'] === 'published' ? 'project-list__item--active' : 'project-list__item--roadmap' }}">
                    <span>{{ str_pad((string) $project['number'], 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <p class="eyebrow">{{ $project['focus'] }} · {{ $project['difficulty'] }}</p>
                        <h3>{{ $project['title'] }}</h3>
                        <p>{{ $project['description'] }}</p>
                        <small>~{{ $project['estimated_minutes'] }} menit · {{ $project['status'] === 'published' ? 'Dapat dikerjakan' : 'Segera hadir' }}</small>
                    </div>
                    @if ($project['status'] === 'published')
                        <a class="text-link" href="{{ route('projects.show', ['projectKey' => $project['key']]) }}">{{ $progress ? 'Lanjutkan' : 'Lihat brief' }} <span aria-hidden="true">→</span></a>
                    @else
                        <span class="status-label">Roadmap</span>
                    @endif
                </article>
            @endforeach
        </section>
    </div>
@endsection
