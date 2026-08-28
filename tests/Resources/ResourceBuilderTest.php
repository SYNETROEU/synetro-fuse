<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests\Resources;

use Synetro\Fuse\Resources\ResourceManager;
use Synetro\Fuse\Resources\ResourceQuery;
use Synetro\Fuse\Tests\Resources\Stubs\CustomProductController;
use Synetro\Fuse\Tests\Resources\Stubs\Product;
use Synetro\Fuse\Tests\Resources\Stubs\ProductPolicy;
use Synetro\Fuse\Tests\TestCase;

class ResourceBuilderTest extends TestCase
{
    public function test_fluent_methods_configure_options(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class);

        $definition = $builder
            ->search(['name', 'sku'])
            ->filter(['status'])
            ->sort(['name'])
            ->include(['category'])
            ->fields(['id', 'name'])
            ->paginate(25)
            ->authorize()
            ->policy(ProductPolicy::class)
            ->middleware(['auth:sanctum'])
            ->uri('custom-products')
            ->controller(CustomProductController::class)
            ->build();

        $this->assertSame('Product', $definition->name());
        $this->assertSame(Product::class, $definition->model());
        $this->assertSame(['name', 'sku'], $definition->search());
        $this->assertSame(['status'], $definition->filter());
        $this->assertSame(['name'], $definition->sort());
        $this->assertSame(['category'], $definition->include());
        $this->assertSame(['id', 'name'], $definition->fields());
        $this->assertSame(25, $definition->paginate());
        $this->assertTrue($definition->authorize());
        $this->assertSame(ProductPolicy::class, $definition->policy());
        $this->assertSame(['auth:sanctum'], $definition->middleware());
        $this->assertSame('custom-products', $definition->uri());
        $this->assertSame(CustomProductController::class, $definition->controller());
    }

    public function test_default_uri_is_generated_from_name(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class);

        $definition = $builder->build();

        $this->assertSame('products', $definition->uri());
    }

    public function test_default_middleware_is_api(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class);

        $definition = $builder->build();

        $this->assertSame(['api'], $definition->middleware());
    }

    public function test_build_query_returns_resource_query(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class)
            ->search(['name'])
            ->filter(['status']);

        $query = $builder->buildQuery();

        $this->assertInstanceOf(ResourceQuery::class, $query);
    }

    public function test_register_stores_definition_in_manager(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class)
            ->search(['name']);

        $builder->register();

        $this->assertSame('Product', $manager->get('Product')->name());
        $this->assertSame(['name'], $manager->get('Product')->search());
    }
}
