<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ComponentRegistry;
use App\Domain\Learning\Content\InvalidLearningComponentException;
use Tests\TestCase;

class ComponentRegistryTest extends TestCase
{
    public function test_practice_is_registered_and_heavy_components_are_only_planned(): void
    {
        $registry = app(ComponentRegistry::class);

        $this->assertSame(['practice'], $registry->registered());
        $this->assertContains('sql', $registry->planned());
        $this->assertContains('visualization', $registry->planned());
    }

    public function test_unknown_component_cannot_be_resolved(): void
    {
        $this->expectException(InvalidLearningComponentException::class);

        app(ComponentRegistry::class)->assertRegistered('arbitrary-script');
    }
}
