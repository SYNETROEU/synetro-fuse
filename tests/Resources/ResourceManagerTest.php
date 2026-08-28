<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests\Resources;

use Synetro\Fuse\Resources\ResourceBuilder;
use Synetro\Fuse\Resources\ResourceDefinition;
use Synetro\Fuse\Resources\ResourceManager;
use Synetro\Fuse\Tests\Resources\Stubs\Category;
use Synetro\Fuse\Tests\Resources\Stubs\Product;
use Synetro\Fuse\Tests\TestCase;

class ResourceManagerTest extends TestCase
{
    public function test_for_returns_resource_builder(): void
    {
        $manager = app(ResourceManager::class);
        $builder = $manager->for(Product::class);

        $this->assertInstanceOf(ResourceBuilder::class, $builder);
    }

    public function test_register_stores_definition(): void
    {
        $manager = app(ResourceManager::class);

        $this->assertNull($manager->get('Product'));

        $resource = new ResourceDefinition(
            name: 'Product',
            model: Product::class,
        );

        $manager->register($resource);

        $this->assertSame($resource, $manager->get('Product'));
    }

    public function test_all_returns_all_definitions(): void
    {
        $manager = app(ResourceManager::class);

        $product = new ResourceDefinition(
            name: 'Product',
            model: Product::class,
        );

        $category = new ResourceDefinition(
            name: 'Category',
            model: Category::class,
        );

        $manager->register($product);
        $manager->register($category);

        $this->assertCount(2, $manager->all());
        $this->assertSame($product, $manager->all()['Product']);
        $this->assertSame($category, $manager->all()['Category']);
    }

    public function test_get_returns_null_for_missing(): void
    {
        $manager = app(ResourceManager::class);

        $this->assertNull($manager->get('NonExistent'));
    }

    public function test_register_throws_on_duplicate_name(): void
    {
        $manager = app(ResourceManager::class);

        $resource = new ResourceDefinition(
            name: 'Product',
            model: Product::class,
        );

        $manager->register($resource);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource [Product] is already registered.');

        $manager->register($resource);
    }

    public function test_register_throws_on_duplicate_uri(): void
    {
        $manager = app(ResourceManager::class);

        $product = new ResourceDefinition(
            name: 'Product',
            model: Product::class,
            uri: 'items',
        );

        $category = new ResourceDefinition(
            name: 'Category',
            model: Category::class,
            uri: 'items',
        );

        $manager->register($product);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource URI [items] is already registered.');

        $manager->register($category);
    }
}
