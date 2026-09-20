<?php

namespace App\Http\Controllers;

use App\Domain\Learner\Assessment\AssessmentContentResolver;
use App\Domain\Learner\Assessment\AttemptStore;
use App\Domain\Learner\Assessment\ExerciseValidator;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\LessonSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use JsonException;

final class AttemptController extends Controller
{
    public function show(Request $request, AssessmentContentResolver $resolver, AttemptStore $attempts): JsonResponse
    {
        $data = $request->validate([
            'exercise_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
        ]);
        $this->resolveExercise($resolver, $data['exercise_key']);

        $attempt = $attempts->find($request->user(), $data['exercise_key']);

        return response()->json(['attempt' => $attempt?->toAttemptArray()]);
    }

    public function check(
        Request $request,
        AssessmentContentResolver $resolver,
        ExerciseValidator $validator,
        AttemptStore $attempts,
    ): JsonResponse {
        $data = $request->validate([
            'exercise_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
            'answer' => ['required', 'array', 'max:20'],
        ]);
        $this->assertAnswerPayloadSize($data['answer']);
        [$source, $exercise] = $this->resolveExercise($resolver, $data['exercise_key']);
        $result = $validator->validate($exercise, $data['answer']);
        $attempt = $attempts->record($request->user(), $data['exercise_key'], $result);

        return response()->json([
            'exercise_key' => $data['exercise_key'],
            'source_hash' => $source->sourceHash,
            'result' => $result->toArray(),
            'attempt' => $attempt->toAttemptArray(),
        ]);
    }

    public function reset(
        Request $request,
        AssessmentContentResolver $resolver,
        AttemptStore $attempts,
    ): JsonResponse {
        $data = $request->validate([
            'exercise_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
        ]);
        $this->resolveExercise($resolver, $data['exercise_key']);
        $attempts->reset($request->user(), $data['exercise_key']);

        return response()->json(['reset' => true]);
    }

    /** @return array{0: LessonSource, 1: array<string, mixed>} */
    private function resolveExercise(AssessmentContentResolver $resolver, string $exerciseKey): array
    {
        try {
            $resolved = $resolver->resolve($exerciseKey);
        } catch (ContentValidationException) {
            throw ValidationException::withMessages([
                'exercise_key' => 'Latihan tidak tersedia untuk disimpan.',
            ]);
        }

        return [$resolved['source'], $resolved['exercise']];
    }

    /** @param array<string, mixed> $answer */
    private function assertAnswerPayloadSize(array $answer): void
    {
        try {
            $encoded = json_encode($answer, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'answer' => 'Jawaban tidak dapat diproses.',
            ]);
        }

        if (strlen($encoded) > 65536) {
            throw ValidationException::withMessages([
                'answer' => 'Jawaban terlalu besar untuk disimpan.',
            ]);
        }
    }
}
