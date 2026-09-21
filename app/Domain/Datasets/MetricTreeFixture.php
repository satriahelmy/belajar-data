<?php

namespace App\Domain\Datasets;

final class MetricTreeFixture
{
    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        return [
            'dataset_key' => 'nusamart',
            'version' => 'v1',
            'trees' => [
                'revenue-tree' => [
                    'title' => 'Revenue tree',
                    'root_id' => 'revenue',
                    'nodes' => [
                        ['id' => 'revenue', 'label' => 'Revenue', 'description' => 'Metric utama yang ingin dipahami.'],
                        ['id' => 'orders', 'label' => 'Orders', 'description' => 'Jumlah order pada periode yang sama.'],
                        ['id' => 'average-order-value', 'label' => 'Average order value', 'description' => 'Revenue rata-rata per order.'],
                        ['id' => 'units-per-order', 'label' => 'Units per order', 'description' => 'Rata-rata jumlah unit dalam satu order.'],
                        ['id' => 'average-price-per-unit', 'label' => 'Average price per unit', 'description' => 'Rata-rata revenue per unit.'],
                    ],
                    'parent_options' => [
                        'orders' => ['revenue'],
                        'average-order-value' => ['revenue'],
                        'units-per-order' => ['average-order-value', 'orders'],
                        'average-price-per-unit' => ['average-order-value', 'units-per-order'],
                    ],
                    'valid_parents' => [
                        'orders' => 'revenue',
                        'average-order-value' => 'revenue',
                        'units-per-order' => 'average-order-value',
                        'average-price-per-unit' => 'average-order-value',
                    ],
                ],
            ],
        ];
    }
}
