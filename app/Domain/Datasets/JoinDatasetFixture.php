<?php

namespace App\Domain\Datasets;

final class JoinDatasetFixture
{
    public function __construct(private readonly SqlDatasetFixture $sqlFixture) {}

    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        $fixture = $this->sqlFixture->payload();
        $tables = [
            'orders' => $fixture['tables']['orders'],
            'order_items' => $fixture['tables']['order_items'],
            'products' => $fixture['tables']['products'],
        ];

        return [
            'dataset_key' => $fixture['dataset_key'],
            'version' => $fixture['version'],
            'tables' => $tables,
            'scenarios' => [
                'order-to-items' => [
                    'label' => 'orders → order_items',
                    'left_table' => 'orders',
                    'right_table' => 'order_items',
                    'join_key' => 'order_id',
                    'left_grain' => 'one row per order',
                    'right_grain' => 'one row per order-item',
                    'warning' => 'Satu order dapat memiliki beberapa item. Setelah JOIN, order_id dapat muncul lebih dari sekali dan metric order-level tidak boleh langsung dijumlahkan.',
                ],
                'items-to-products' => [
                    'label' => 'order_items → products',
                    'left_table' => 'order_items',
                    'right_table' => 'products',
                    'join_key' => 'product_id',
                    'left_grain' => 'one row per order-item',
                    'right_grain' => 'one row per product',
                    'warning' => 'JOIN many-to-one ini menambahkan konteks product ke setiap order-item. Grain hasil tetap satu row per order-item selama product_id unik di tabel products.',
                ],
                'wrong-key' => [
                    'label' => 'orders → order_items dengan product_id',
                    'left_table' => 'orders',
                    'right_table' => 'order_items',
                    'join_key' => 'product_id',
                    'left_grain' => 'one row per order',
                    'right_grain' => 'one row per order-item',
                    'warning' => 'Key ini tidak menghubungkan kedua tabel. Hasil kosong adalah sinyal untuk memeriksa relationship, bukan hasil analisis.',
                ],
            ],
        ];
    }
}
