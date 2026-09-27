<?php

namespace App\Console\Commands;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Datasets\DatasetManifestRepository;
use App\Domain\Datasets\DatasetValidationException;
use App\Domain\Projects\ProjectRepository;
use Illuminate\Console\Command;

class ValidateContent extends Command
{
    protected $signature = 'content:validate';

    protected $description = 'Validate repository content, structured learning blocks, and dataset manifests.';

    public function handle(
        ContentRepository $repository,
        MarkdownLessonRenderer $renderer,
        DatasetManifestRepository $datasets,
        ProjectRepository $projects,
    ): int
    {
        try {
            $keys = $repository->validate();

            foreach ($keys as $key) {
                $rendered = $renderer->render($key);
                $this->line("Validated {$key} (".count($rendered->headings).' headings)');
            }

            foreach ($datasets->validateAll() as $dataset) {
                $this->line("Validated dataset {$dataset['dataset_key']}/{$dataset['version']}");
            }

            foreach ($projects->validate() as $projectKey) {
                $this->line("Validated project {$projectKey}");
            }
        } catch (ContentValidationException|DatasetValidationException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Content and dataset validation passed.');

        return self::SUCCESS;
    }
}
