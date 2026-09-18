<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FoundationSmokeTest extends TestCase
{
    public function test_the_foundation_view_is_server_rendered(): void
    {
        $response = $this->get('/__foundation');

        $response
            ->assertOk()
            ->assertSee('BelajarData')
            ->assertSee('Laravel');
    }

    public function test_the_application_can_connect_to_the_local_mysql_database(): void
    {
        $result = DB::selectOne('SELECT 1 AS healthy');

        $this->assertSame(1, (int) $result->healthy);
        $this->assertSame('belajar_data_v2', DB::connection()->getDatabaseName());
    }
}
