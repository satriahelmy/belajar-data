<nav class="lesson-section-nav {{ $mobile ? 'lesson-section-nav--mobile' : '' }}" aria-label="Daftar bagian lesson">
    <p class="eyebrow">Topik {{ str_pad((string) $navigation['current']['order'], 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $navigation['total'], 2, '0', STR_PAD_LEFT) }}</p>
    <p class="lesson-nav__label">Di halaman ini</p>
    @if ($lesson->headings !== [])
        <ol>
            @foreach ($lesson->headings as $heading)
                <li data-heading-level="{{ $heading['level'] }}"><a href="#{{ $heading['slug'] }}"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $heading['text'] }}</a></li>
            @endforeach
        </ol>
    @endif
</nav>
