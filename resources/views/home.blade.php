@extends('layouts.public', ['active' => null])

@section('title', 'Belajar data, tanpa bingung mulai dari mana | BelajarData')

@section('content')
    <div class="home-page">
        <section class="home-hero site-container">
            <div class="home-hero__copy">
                <p class="eyebrow">Data Analyst Path</p>
                <h1>Belajar data, tanpa bingung mulai dari mana.</h1>
                <p class="lede">Jalur belajar yang membantu kamu memahami masalah, bekerja dengan data, dan menyampaikan insight dengan lebih percaya diri.</p>
                <div class="button-row">
                    <a class="button button--primary" href="{{ route('learning.module', ['moduleKey' => $firstModule['key']]) }}">Mulai belajar <span aria-hidden="true">→</span></a>
                    <a class="button button--quiet" href="{{ route('learning.index') }}">Lihat learning path</a>
                </div>
                <p class="quiet-note">Kamu bisa mulai belajar tanpa login.</p>
            </div>

            <figure class="analysis-artifact" aria-labelledby="analysis-artifact-title">
                <figcaption class="analysis-artifact__header"><span id="analysis-artifact-title">Contoh analisis</span><span>{{ $dataset['name'] }} · {{ $dataset['version'] }}</span></figcaption>
                <div class="analysis-artifact__step">
                    <span class="analysis-artifact__number">01</span>
                    <div><p class="analysis-artifact__label">Pertanyaan</p><p class="analysis-artifact__value">Bagaimana revenue berubah menurut category?</p></div>
                </div>
                <div class="analysis-artifact__connector" aria-hidden="true">↓</div>
                <div class="analysis-artifact__step">
                    <span class="analysis-artifact__number">02</span>
                    <div><p class="analysis-artifact__label">Data</p><div class="dataset-tables">@foreach ($dataset['tables'] as $table)<span>{{ $table['key'] }}</span>@endforeach</div><p class="analysis-artifact__note">{{ $dataset['grain'] }}</p></div>
                </div>
                <div class="analysis-artifact__connector" aria-hidden="true">↓</div>
                <div class="analysis-artifact__step">
                    <span class="analysis-artifact__number">03</span>
                    <div><p class="analysis-artifact__label">Analisis</p><pre><code>SELECT p.category,
       SUM(t.revenue) AS revenue
FROM transactions AS t
JOIN products AS p ON p.product_id = t.product_id
GROUP BY p.category;</code></pre></div>
                </div>
                <div class="analysis-artifact__connector" aria-hidden="true">↓</div>
                <div class="analysis-artifact__step analysis-artifact__step--insight">
                    <span class="analysis-artifact__number">04</span>
                    <div><p class="analysis-artifact__label">Insight</p><p class="analysis-artifact__value">Mulai dari grain dan definisi metric sebelum memilih tool.</p></div>
                </div>
            </figure>
        </section>

        <section class="home-section home-section--path home-section--bordered">
            <div class="site-container">
                <div class="section-heading"><div><p class="eyebrow">Learning path</p><h2>Satu jalur belajar, dari cara berpikir sampai mengambil keputusan dari data.</h2></div><a class="text-link" href="{{ route('learning.index') }}">Lihat seluruh path <span aria-hidden="true">→</span></a></div>
                <div class="path-journey" aria-label="Urutan lima fase Data Analyst Path">
                    @foreach ($phases as $phase)
                        <section class="path-phase" aria-labelledby="home-phase-{{ $phase['key'] }}">
                            <header class="path-phase__header"><span class="path-phase__number">{{ str_pad((string) $phase['order'], 2, '0', STR_PAD_LEFT) }}</span><div><p class="eyebrow">Phase {{ $phase['order'] }}</p><h3 id="home-phase-{{ $phase['key'] }}">{{ $phase['title'] }}</h3></div><p>{{ $phase['description'] }}</p></header>
                            <ol class="path-phase__modules path-phase__modules--compact">
                                @foreach ($phase['modules'] as $module)
                                    <li><span class="path-module__number">{{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</span>@if ($module['published'])<a href="{{ route('learning.module', ['moduleKey' => $module['key']]) }}">{{ $module['title'] }}</a>@else<span>{{ $module['title'] }}</span>@endif</li>
                                @endforeach
                            </ol>
                        </section>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="learning-model home-section home-section--ink">
            <div class="site-container">
                <p class="eyebrow">Model belajar</p>
                <ol class="learning-sequence">
                    <li><span>01</span><strong>Learn</strong><p>Pahami konsep dan cara berpikirnya.</p></li>
                    <li><span>02</span><strong>Practice</strong><p>Coba keputusan analitis dalam latihan kecil.</p></li>
                    <li><span>03</span><strong>Challenge</strong><p>Gabungkan beberapa konsep dalam kasus.</p></li>
                    <li><span>04</span><strong>Project</strong><p>Kerjakan analisis yang lebih utuh.</p></li>
                </ol>
            </div>
        </section>

        <section class="home-section home-section--soft">
            <div class="site-container home-split">
                <div class="section-intro"><p class="eyebrow">Setelah core path</p><h2>Projects membawa skill ke konteks bisnis.</h2><p>Mulai dari NusaMart Revenue Slowdown, lalu latih cara menyusun evidence, validasi, dan komunikasi.</p><a class="text-link" href="{{ route('projects.index') }}">Lihat arah projects <span aria-hidden="true">→</span></a></div>
                <div class="project-preview">
                    <div><span>Project 01</span><strong>NusaMart Revenue Slowdown</strong><small>Performance diagnosis</small></div>
                    <div><span>Project 02</span><strong>Customer Retention Analysis</strong><small>Customer behavior over time</small></div>
                    <div><span>Project 03</span><strong>Delivery Performance Investigation</strong><small>Operational investigation</small></div>
                </div>
            </div>
        </section>

    </div>
@endsection
