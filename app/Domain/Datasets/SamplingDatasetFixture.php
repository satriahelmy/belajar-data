<?php

namespace App\Domain\Datasets;

final class SamplingDatasetFixture
{
    public function __construct(private readonly VisualizationDatasetFixture $visualizations) {}

    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        $source = $this->visualizations->payload();

        return [
            'dataset_key' => $source['dataset_key'],
            'version' => $source['version'],
            'grain' => $source['grain'],
            'rows' => array_map(static fn (array $row): array => [
                'order_id' => $row['order_id'],
                'revenue' => $row['revenue'],
                'quantity' => $row['quantity'],
            ], $source['rows']),
        ];
    }

    /** @return array<string, mixed> */
    public function summary(string $metric, int $sampleSize, int $repeats, int $seed): array
    {
        $rows = $this->payload()['rows'];
        $values = array_map(static fn (array $row): float => (float) $row[$metric], $rows);
        $populationMean = $this->mean($values);
        $estimates = [];

        for ($repeat = 1; $repeat <= $repeats; $repeat++) {
            $sample = $this->sample($values, $sampleSize, $seed, $repeat);
            $mean = $this->mean($sample);
            $estimates[] = [
                'repeat' => $repeat,
                'sample_mean' => self::round($mean),
                'delta' => self::round($mean - $populationMean),
            ];
        }

        $sampleMeans = array_column($estimates, 'sample_mean');

        return [
            'population_mean' => self::round($populationMean),
            'estimate_min' => self::round(min($sampleMeans)),
            'estimate_max' => self::round(max($sampleMeans)),
            'estimates' => $estimates,
        ];
    }

    /** @param list<float> $values @return list<float> */
    private function sample(array $values, int $size, int $seed, int $repeat): array
    {
        $indices = range(0, count($values) - 1);
        $state = ($seed + ($repeat * 7919)) % 233280;

        for ($index = count($indices) - 1; $index > 0; $index--) {
            $state = (($state * 9301) + 49297) % 233280;
            $swap = (int) floor(($state / 233280) * ($index + 1));
            [$indices[$index], $indices[$swap]] = [$indices[$swap], $indices[$index]];
        }

        return array_map(static fn (int $index): float => $values[$index], array_slice($indices, 0, $size));
    }

    /** @param list<float> $values */
    private function mean(array $values): float
    {
        return $values === [] ? 0 : array_sum($values) / count($values);
    }

    private static function round(float $value): int|float
    {
        $rounded = round($value, 2);

        return $rounded === (float) (int) $rounded ? (int) $rounded : $rounded;
    }
}
