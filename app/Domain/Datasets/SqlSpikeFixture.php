<?php

namespace App\Domain\Datasets;

final class SqlSpikeFixture
{
    public function __construct(private readonly SqlDatasetFixture $fixture) {}

    /** @return array<string, mixed> */
    public function payload(): array
    {
        return $this->fixture->payload();
    }
}
