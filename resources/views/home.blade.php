@extends('layouts.public', ['active' => null])

@section('title', 'Belajar data, tanpa bingung mulai dari mana — BelajarData')

@section('content')
    <div class="home-page">
        <section class="home-hero site-container">
            <div class="home-hero__copy">
                <p class="eyebrow">Data Analyst Path</p>
                <h1>Belajar data, tanpa bingung mulai dari mana.</h1>
                <p class="lede">Jalur belajar yang membantu kamu memahami masalah, bekerja dengan data, dan menyampaikan insight dengan lebih percaya diri.</p>
                <div class="button-row">
                    <a class="button button--primary" href="{{ route('learning.module', ['moduleKey' => $firstModule['key']]) }}">Mulai dari Module 01</a>
                    <a class="button button--quiet" href="{{ route('learning.index') }}">Lihat learning path</a>
                </div>
                <p class="quiet-note">Kamu bisa mulai belajar tanpa login.</p>
            </div>
            <div class="path-preview" aria-label="Pratinjau urutan belajar">
                <p class="path-preview__label">Recommended path</p>
                @foreach ($phases as $phase)
                    <div class="path-preview__phase">
                        <div class="path-preview__phase-heading"><span>{{ str_pad((string) $phase['order'], 2, '0', STR_PAD_LEFT) }}</span><p class="phase-label">{{ $phase['title'] }}</p></div>
                        @foreach (array_slice($phase['modules'], 0, 4) as $module)
                            <div class="path-preview__row">
                                <span class="path-preview__number">{{ str_pad((string) $module['number'], 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $module['title'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                <a class="text-link" href="{{ route('learning.index') }}">Lihat seluruh path <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section class="home-section home-section--bordered">
            <div class="site-container">
                <div class="section-intro"><p class="eyebrow">Cara belajar</p><h2>Konsep dulu. Praktik secukupnya. Lalu gunakan dalam kasus.</h2></div>
                <ol class="learning-sequence">
                    <li><span>01</span><strong>Learn</strong><p>Pahami konsep dan cara berpikirnya.</p></li>
                    <li><span>02</span><strong>Practice</strong><p>Coba keputusan analitis dalam latihan kecil.</p></li>
                    <li><span>03</span><strong>Challenge</strong><p>Gabungkan beberapa konsep dalam kasus.</p></li>
                    <li><span>04</span><strong>Project</strong><p>Kerjakan analisis yang lebih utuh.</p></li>
                </ol>
            </div>
        </section>

        <section class="home-section home-section--artifact">
            <div class="site-container home-split">
                <div class="section-intro"><p class="eyebrow">Mulai dari reasoning</p><h2>Tools membantu. Pertanyaan analitis memimpin.</h2><p>BelajarData menyusun skill berdasarkan pekerjaan yang perlu dilakukan seorang analyst, bukan berdasarkan daftar fitur tool.</p></div>
                <div class="artifact-preview" aria-label="Contoh learning artifact">
                    <div class="artifact-preview__bar"><span>Example question</span><span>SQL · later in the path</span></div>
                    <p>Bagaimana revenue berubah menurut category?</p>
                    <pre><code>SELECT category,
       SUM(revenue) AS revenue
FROM orders
GROUP BY category;</code></pre>
                    <p class="artifact-preview__note">Setiap tool dipelajari untuk menjawab pertanyaan yang jelas.</p>
                </div>
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

        <section class="home-cta site-container"><p class="eyebrow">Satu langkah pertama</p><h2>Mulai dengan memahami cara berpikir analyst.</h2><a class="button button--primary" href="{{ route('learning.module', ['moduleKey' => $firstModule['key']]) }}">Buka Module 01</a></section>
    </div>
@endsection
