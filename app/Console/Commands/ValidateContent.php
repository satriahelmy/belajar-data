<?php

namespace App\Console\Commands;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Console\Command;

class ValidateContent extends Command
{
    protected $signature = 'content:validate';

    protected $description = 'Validate and render the current repository content slice (Gate D prototype).';

    public function handle(ContentRepository $repository, MarkdownLessonRenderer $renderer): int
    {
        $keys = $repository->lessonKeys();

        if ($keys === []) {
            $this->error('No lesson.md files were found under content/data-analyst.');

            return self::FAILURE;
        }

        foreach ($keys as $key) {
            try {
                $rendered = $renderer->render($key);
                $this->line("Validated {$key} (".count($rendered->headings).' headings)');
            } catch (ContentValidationException $exception) {
                $this->error("{$key}: {$exception->getMessage()}");

                return self::FAILURE;
            }
        }

        $this->info('Content validation passed for the Gate D representative slice.');

        return self::SUCCESS;
    }
}
