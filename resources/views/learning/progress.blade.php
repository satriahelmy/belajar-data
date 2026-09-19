@extends('layouts.public', ['active' => 'progress'])

@section('title', 'Progress | BelajarData')

@section('content')
    <div class="page-shell site-container learner-page">
        <header class="page-intro learner-page__intro">
            <p class="eyebrow">Progress belajar</p>
            <h1>Lanjutkan dari tempat terakhir.</h1>
            <p class="lede">Progress membantu kamu kembali ke materi yang sedang dipelajari. Urutan belajar tetap fleksibel.</p>
            <dl class="learner-summary" aria-label="Ringkasan progress">
                <div><dt>Topic selesai</dt><dd>{{ $completedCount }}</dd></div>
                <div><dt>Sedang dipelajari</dt><dd>{{ $startedCount }}</dd></div>
                <div><dt>Topic tersedia</dt><dd>{{ $publishedTopicCount }}</dd></div>
            </dl>
        </header>

        <div class="learner-layout">
            <section class="learner-panel" aria-labelledby="recent-title">
                <div class="section-heading-row"><div><p class="eyebrow">Riwayat belajar</p><h2 id="recent-title">Terakhir dipelajari</h2></div></div>
                @if ($recentTopics->isEmpty())
                    <div class="empty-state"><strong>Belum ada riwayat belajar.</strong><p>Buka topic yang ingin kamu pelajari, lalu progress-nya akan muncul di sini.</p><a class="text-link" href="{{ route('learning.index') }}">Buka learning path <span aria-hidden="true">→</span></a></div>
                @else
                    <ol class="learner-list">
                        @foreach ($recentTopics as $topic)
                            <li class="learner-list__item">
                                <span class="status-dot {{ $topic['status'] === 'completed' ? 'status-dot--available' : '' }}" aria-hidden="true"></span>
                                <div><p class="eyebrow">{{ $topic['module_title'] }}</p><h3><a href="{{ $topic['url'] }}">{{ $topic['title'] }}</a></h3><p class="quiet-note">{{ $topic['status'] === 'completed' ? 'Topic selesai' : 'Sedang dipelajari' }}</p></div>
                                <a class="learner-list__action" href="{{ $topic['url'] }}">{{ $topic['status'] === 'completed' ? 'Buka lagi' : 'Lanjutkan' }} <span aria-hidden="true">→</span></a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            <section class="learner-panel" aria-labelledby="bookmarks-title">
                <div class="section-heading-row"><div><p class="eyebrow">Disimpan</p><h2 id="bookmarks-title">Bookmark</h2></div></div>
                @if ($bookmarks->isEmpty())
                    <div class="empty-state"><strong>Belum ada lesson yang disimpan.</strong><p>Gunakan tombol Simpan lesson pada halaman lesson untuk membuat daftar bacaanmu.</p></div>
                @else
                    <ul class="learner-list learner-list--bookmarks" data-bookmark-list>
                        @foreach ($bookmarks as $bookmark)
                            <li class="learner-list__item" data-bookmark-item>
                                <div><p class="eyebrow">{{ $bookmark['module_title'] }}</p><h3><a href="{{ $bookmark['url'] }}">{{ $bookmark['title'] }}</a></h3></div>
                                <form method="POST" action="{{ route('bookmarks.toggle') }}" data-bookmark-form>@csrf<input type="hidden" name="content_type" value="topic"><input type="hidden" name="content_key" value="{{ $bookmark['content_key'] }}"><button class="button button--quiet" type="submit" data-bookmark-action aria-pressed="true">Hapus</button></form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
@endsection
