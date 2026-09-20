<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ComponentRegistry;
use App\Domain\Learning\Content\InvalidLearningComponentException;
use Tests\TestCase;

class ComponentRegistryTest extends TestCase
{
    public function test_practice_sql_and_spreadsheet_are_registered_while_other_heavy_components_are_only_planned(): void
    {
        $registry = app(ComponentRegistry::class);

        $this->assertSame(['practice', 'sql-playground', 'spreadsheet-playground'], $registry->registered());
        $this->assertNotContains('sql', $registry->planned());
        $this->assertNotContains('spreadsheet', $registry->planned());
        $this->assertContains('visualization', $registry->planned());
    }

    public function test_unknown_component_cannot_be_resolved(): void
    {
        $this->expectException(InvalidLearningComponentException::class);

        app(ComponentRegistry::class)->assertRegistered('arbitrary-script');
    }
}
