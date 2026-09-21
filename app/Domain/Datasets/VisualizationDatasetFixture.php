<?php

namespace App\Domain\Datasets;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

final class VisualizationDatasetFixture
{
    public function __construct(private readonly DatasetManifestRepository $manifests) {}

    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        $contract = $this->manifests->load('nusamart', 'v1');
        $manifest = $contract['manifest'];
        $transactions = $this->readRows($contract['directory'].'/'.($manifest['files']['transactions'] ?? ''), 'transactions');
        $products = $this->readRows($contract['directory'].'/'.($manifest['files']['products'] ?? ''), 'products');
        $productCategories = [];

        foreach ($products as $product) {
            if (! is_string($product['product_id'] ?? null) || ! is_string($product['category'] ?? null)) {
                throw new RuntimeException('Visualization product fixture has an invalid product category.');
            }

            $productCategories[$product['product_id']] = $product['category'];
        }

        $rows = [];
        foreach ($transactions as $transaction) {
            $productId = $transaction['product_id'] ?? null;
            $date = $transaction['order_date'] ?? null;

            if (! is_string($productId) || ! is_string($date) || ! isset($productCategories[$productId])) {
                throw new RuntimeException('Visualization transaction fixture has an invalid product relationship.');
            }

            $rows[] = [
                'order_id' => (string) $transaction['order_id'],
                'order_date' => $date,
                'month' => substr($date, 0, 7),
                'region' => (string) $transaction['region'],
                'channel' => (string) $transaction['channel'],
                'category' => $productCategories[$productId],
                'quantity' => (int) $transaction['quantity'],
                'revenue' => (float) $transaction['revenue'],
            ];
        }

        $dimensions = ['month', 'category', 'region', 'channel'];
        $views = [];

        foreach ($dimensions as $dimension) {
            $views[$dimension] = $this->aggregate($rows, $dimension);
        }

        return [
            'dataset_key' => $manifest['dataset_key'],
            'version' => $manifest['version'],
            'grain' => $manifest['grain'],
            'rows' => $rows,
            'views' => $views,
        ];
    }

    /** @return list<array<string, mixed>> */
    private function readRows(string $path, string $table): array
    {
        if ($path === '' || str_contains($path, '..')) {
            throw new RuntimeException("Invalid visualization table contract [{$table}].");
        }

        try {
            $rows = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid visualization fixture JSON [{$path}].", previous: $exception);
        }

        if (! is_array($rows) || ! array_is_list($rows)) {
            throw new RuntimeException("Visualization table [{$table}] must be a list of rows.");
        }

        return $rows;
    }

    /** @param list<array<string, mixed>> $rows @return array<string, mixed> */
    private function aggregate(array $rows, string $dimension): array
    {
        $groups = [];

        foreach ($rows as $row) {
            $label = (string) $row[$dimension];
            $groups[$label] ??= [
                'label' => $label,
                'revenue' => 0.0,
                'quantity' => 0,
                'orders' => [],
            ];
            $groups[$label]['revenue'] += (float) $row['revenue'];
            $groups[$label]['quantity'] += (int) $row['quantity'];
            $groups[$label]['orders'][$row['order_id']] = true;
        }

        $result = array_map(static function (array $group): array {
            $orders = count($group['orders']);

            return [
                'label' => $group['label'],
                'revenue' => self::round($group['revenue']),
                'quantity' => $group['quantity'],
                'orders' => $orders,
                'average_order_value' => $orders > 0 ? self::round($group['revenue'] / $orders) : 0,
            ];
        }, array_values($groups));

        usort($result, static fn (array $left, array $right): int => strcmp($left['label'], $right['label']));

        return [
            'dimension' => $dimension,
            'rows' => $result,
        ];
    }

    private static function round(float $value): int|float
    {
        $rounded = round($value, 2);

        return $rounded === (float) (int) $rounded ? (int) $rounded : $rounded;
    }
}
