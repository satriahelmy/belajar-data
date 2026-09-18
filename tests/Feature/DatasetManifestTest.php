<?php

namespace Tests\Feature;

use App\Domain\Datasets\DatasetManifestRepository;
use App\Domain\Datasets\DatasetValidationException;
use Tests\TestCase;

class DatasetManifestTest extends TestCase
{
    public function test_versioned_dataset_manifest_and_dictionary_load(): void
    {
        $result = app(DatasetManifestRepository::class)->load('nusamart', 'v1');

        $this->assertSame('nusamart', $result['manifest']['dataset_key']);
        $this->assertSame(['transactions', 'products'], array_keys($result['manifest']['tables']));
        $this->assertSame(['transactions', 'products'], array_keys($result['data_dictionary']['tables']));
    }

    public function test_invalid_dataset_key_is_rejected(): void
    {
        $this->expectException(DatasetValidationException::class);

        app(DatasetManifestRepository::class)->load('../outside');
    }
}
