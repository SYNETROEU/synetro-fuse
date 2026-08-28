<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Features\Feature;
use Synetro\Fuse\Features\FeatureManager;

class FeatureManagerTest extends TestCase
{
    public function test_feature_manager_can_check_features(): void
    {
        $manager = app(FeatureManager::class);
        $feature = $manager->for('test-feature');

        $this->assertInstanceOf(Feature::class, $feature);
    }

    public function test_feature_can_be_checked_for_context(): void
    {
        $manager = app(FeatureManager::class);
        $feature = $manager->for('test-feature');

        $this->assertFalse($feature->enabled());
    }
}
