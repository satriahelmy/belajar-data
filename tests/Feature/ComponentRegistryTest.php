<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ComponentRegistry;
use App\Domain\Learning\Content\InvalidLearningComponentException;
use Tests\TestCase;

class ComponentRegistryTest extends TestCase
{
    public function test_production_learning_components_are_registered(): void
    {
        $registry = app(ComponentRegistry::class);

        $this->assertSame(['practice', 'sql-playground', 'spreadsheet-playground', 'python-practice', 'visualization-playground', 'join-grain-playground', 'sampling-uncertainty-playground', 'metric-tree-builder', 'communication-builder'], $registry->registered());
        $this->assertNotContains('sql', $registry->planned());
        $this->assertNotContains('spreadsheet', $registry->planned());
        $this->assertNotContains('python', $registry->planned());
        $this->assertNotContains('visualization', $registry->planned());
        $this->assertNotContains('sampling-uncertainty', $registry->planned());
        $this->assertNotContains('metric-tree', $registry->planned());
        $this->assertNotContains('communication', $registry->planned());
    }

    public function test_unknown_component_cannot_be_resolved(): void
    {
        $this->expectException(InvalidLearningComponentException::class);

        app(ComponentRegistry::class)->assertRegistered('arbitrary-script');
    }
}
