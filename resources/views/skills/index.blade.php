@extends('layouts.public', ['active' => 'skills'])

@section('title', 'Explore Skills | BelajarData')

@section('content')
    <div class="page-shell site-container"><header class="page-intro page-intro--compact"><p class="eyebrow">Explore Skills</p><h1>Masuk dari skill yang sedang kamu butuhkan.</h1><p class="lede">Explore Skills adalah indeks ke content canonical di Data Analyst Path, bukan curriculum kedua.</p></header><div class="skills-list">
        @foreach ($skills as $skill)<article class="skill-row"><div><h2>{{ $skill['title'] }}</h2><p>{{ $skill['topic']['title'] ?? $skill['module_title'] }}</p></div><div class="skill-row__action">@if ($skill['topic'] !== null && $skill['module_status'] === 'published')<a class="text-link" href="{{ route('learning.lesson', ['pathKey' => 'data-analyst', 'moduleKey' => $skill['module_key'], 'topicKey' => $skill['topic_key']]) }}">Buka topic <span aria-hidden="true">→</span></a>@else<a class="text-link" href="{{ route('learning.index') }}#module-{{ $skill['module_key'] }}">Lihat di path <span aria-hidden="true">→</span></a>@endif</div></article>@endforeach
    </div></div>
@endsection
