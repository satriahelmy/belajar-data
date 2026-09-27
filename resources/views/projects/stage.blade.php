@extends('layouts.public', ['active' => 'projects'])

@section('title', $stage['title'].' · '.$project['title'].' | BelajarData')

@section('content')
    <div class="page-shell site-container project-page">
        <a class="back-link" href="{{ route('projects.show', ['projectKey' => $project['key']]) }}"><span aria-hidden="true">←</span> Kembali ke project</a>
        <header class="project-stage-header">
            <p class="eyebrow">Project {{ str_pad((string) $project['number'], 2, '0', STR_PAD_LEFT) }} · Stage {{ str_pad((string) $stage['number'], 2, '0', STR_PAD_LEFT) }} dari 06</p>
            <h1>{{ $stage['title'] }}</h1>
            <p class="lede">{{ $project['title'] }}</p>
        </header>

        <div class="project-workspace">
            <article class="lesson-reading project-reading">
                <div class="lesson-content">{!! $lesson->html !!}</div>

                <section class="project-checkpoint" aria-labelledby="project-checkpoint-title">
                    <p class="eyebrow">Checkpoint</p>
                    <h2 id="project-checkpoint-title">{{ $exercise['prompt'] }}</h2>
                    <form method="POST" action="{{ route('projects.stage.check', ['projectKey' => $project['key'], 'stageKey' => $stageKey]) }}">
                        @csrf
                        @if ($exercise['type'] === 'multiple_choice')
                            <fieldset class="practice-options">
                                <legend class="sr-only">Pilih satu jawaban</legend>
                                @foreach ($exercise['options'] as $index => $option)
                                    <label class="practice-option"><input type="radio" name="option" value="{{ $index }}" @checked(($attempt?->answer_json['option'] ?? null) === $index)> <span>{{ $option }}</span></label>
                                @endforeach
                            </fieldset>
                        @elseif ($exercise['type'] === 'multi_select')
                            <fieldset class="practice-options">
                                <legend class="sr-only">Pilih semua yang sesuai</legend>
                                @foreach ($exercise['options'] as $index => $option)
                                    <label class="practice-option"><input type="checkbox" name="options[]" value="{{ $index }}" @checked(in_array($index, $attempt?->answer_json['options'] ?? [], true))> <span>{{ $option }}</span></label>
                                @endforeach
                            </fieldset>
                        @elseif ($exercise['type'] === 'numeric')
                            <label class="project-answer-label" for="project-answer">Jawaban angka</label>
                            <input class="practice-number" id="project-answer" name="value" type="number" step="any" value="{{ $attempt?->answer_json['value'] ?? '' }}">
                        @elseif ($exercise['type'] === 'text_self_assessment')
                            <label class="project-answer-label" for="project-answer">Jawabanmu</label>
                            <textarea class="practice-writing" id="project-answer" name="text" rows="7">{{ $attempt?->answer_json['text'] ?? '' }}</textarea>
                            <fieldset class="practice-review">
                                <legend>Sebelum menandai selesai, cek jawabanmu</legend>
                                @foreach ($exercise['checklist'] as $index => $item)
                                    <label><input type="checkbox" name="checklist[{{ $index }}]" value="1" @checked(($attempt?->answer_json['checklist'][$index] ?? false) === true)> {{ $item }}</label>
                                @endforeach
                                <label><input type="checkbox" name="complete" value="1"> Saya sudah membandingkan jawaban dengan checklist.</label>
                            </fieldset>
                        @endif
                        <button class="button button--primary" type="submit">Simpan dan cek</button>
                    </form>
                    @if ($feedback)
                        <p class="project-feedback {{ $feedback['valid'] ? 'is-valid' : 'is-invalid' }}" role="status">{{ $feedback['text'] }}</p>
                    @endif
                </section>

                <div class="project-stage-actions">
                    <a class="text-link" href="{{ route('projects.reference', ['projectKey' => $project['key']]) }}">{{ $referenceUnlocked ? 'Buka Reference Approach' : 'Lihat cara membuka Reference Approach' }} <span aria-hidden="true">→</span></a>
                    <a class="text-link" href="{{ route('projects.show', ['projectKey' => $project['key']]) }}">Kembali ke daftar stage</a>
                </div>
            </article>

            <aside class="project-stage-sidebar" aria-label="Navigasi stage project">
                <p class="eyebrow">Di project ini</p>
                @foreach ($project['stage_keys'] as $navStageKey)
                    @php($navStage = $project['stages'][$navStageKey])
                    <a class="project-stage-nav {{ $navStageKey === $stageKey ? 'is-current' : '' }}" href="{{ route('projects.stage', ['projectKey' => $project['key'], 'stageKey' => $navStageKey]) }}">
                        <span>{{ str_pad((string) $navStage['number'], 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $navStage['title'] }}</strong>
                        <small>{{ ($statuses[$navStageKey] ?? false) ? 'Selesai' : 'Belum selesai' }}</small>
                    </a>
                @endforeach
            </aside>
        </div>
    </div>
@endsection
