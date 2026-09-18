<?php

namespace Tests\Feature;

use App\Domain\Datasets\SqlSpikeFixture;
use Tests\TestCase;

class SqlSpikeTest extends TestCase
{
    public function test_gate_a_route_exposes_only_the_static_fixture_and_spike_shell(): void
    {
        $this->get('/__spike/sql')
            ->assertOk()
            ->assertSee('Browser-side SQL execution')
            ->assertSee('orders')
            ->assertSee('order_items')
            ->assertSee('No query is sent to Laravel or MySQL')
            ->assertDontSee('DB_PASSWORD');
    }

    public function test_gate_a_fixture_contains_the_required_relational_grain(): void
    {
        $payload = app(SqlSpikeFixture::class)->payload();

        $this->assertSame(['orders', 'order_items', 'products', 'customers'], array_keys($payload['tables']));
        $this->assertCount(3, $payload['relationships']);
        $this->assertSame(6, count($payload['tables']['orders']['rows']));
        $this->assertSame(9, count($payload['tables']['order_items']['rows']));
    }
}
