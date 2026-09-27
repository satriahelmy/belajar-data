<?php

namespace Tests\Feature;

use App\Domain\Datasets\DatasetManifestRepository;
use Tests\TestCase;

class TableauLearningTest extends TestCase
{
    public function test_tableau_dataset_page_exposes_versioned_files_and_checksums(): void
    {
        $response = $this->get('/downloads/tableau/nusamart/v1');

        $response->assertOk()
            ->assertSee('NusaMart V1')
            ->assertSee('transactions.csv')
            ->assertSee('products.csv')
            ->assertSee('orders.csv')
            ->assertSee('customers.csv')
            ->assertSee('SHA-256')
            ->assertSee('Jangan mengunggah data perusahaan');

        $this->assertMatchesRegularExpression('/[a-f0-9]{64}/', $response->getContent());
    }

    public function test_tableau_dataset_downloads_are_manifest_bound_and_csv_only(): void
    {
        $this->get('/downloads/tableau/nusamart/v1/transactions')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename=nusamart-v1-transactions.csv');

        $this->get('/downloads/tableau/nusamart/v1/not-a-file')->assertNotFound();

        $dataset = app(DatasetManifestRepository::class)->load('nusamart', 'v1');
        $this->assertSame('tableau/transactions.csv', $dataset['manifest']['files']['tableau_transactions']);
    }

    public function test_tableau_module_and_challenge_render_the_external_workflow_contract(): void
    {
        $this->get('/learn/data-analyst/10-tableau-for-data-analysis/01-tableau-workflow')
            ->assertOk()
            ->assertSee('dataset Tableau NusaMart')
            ->assertSee('/downloads/tableau/nusamart/v1', false)
            ->assertSee('tableau-download-01');

        $this->get('/learn/data-analyst/10-tableau-for-data-analysis/challenge')
            ->assertOk()
            ->assertSee('NusaMart Tableau Workflow')
            ->assertSee('Tableau Public')
            ->assertSee('tableau-challenge-reflection-01');
    }

    public function test_tableau_content_does_not_offer_power_bi_or_workbook_parsing(): void
    {
        $this->get('/learn/data-analyst/10-tableau-for-data-analysis/03-tableau-dashboard-validation')
            ->assertOk()
            ->assertDontSee('Power BI')
            ->assertDontSee('workbook parser');
    }
}
