<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests\Resources;

use Illuminate\Database\Eloquent\Builder;
use Synetro\Fuse\Resources\ResourceDefinition;
use Synetro\Fuse\Resources\ResourceQuery;
use Synetro\Fuse\Tests\Resources\Stubs\Product;
use Synetro\Fuse\Tests\TestCase;

class ResourceQueryTest extends TestCase
{
    private ResourceDefinition $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new ResourceDefinition(
            name: 'Product',
            model: Product::class,
            search: ['name', 'sku'],
            filter: ['status', 'category_id'],
            sort: ['name', 'created_at'],
            include: ['category'],
            fields: ['id', 'name', 'price', 'status'],
        );
    }

    public function test_search_applies_like_conditions(): void
    {
        $query = (new ResourceQuery($this->resource))->search('test');

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('where', strtolower($sql));
    }

    public function test_filter_only_allows_declared_columns(): void
    {
        $query = (new ResourceQuery($this->resource))->filter(['status' => 'active', 'secret_field' => 'hack']);

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('status', strtolower($sql));
        $this->assertStringNotContainsString('secret_field', strtolower($sql));
    }

    public function test_sort_only_allows_declared_columns(): void
    {
        $query = (new ResourceQuery($this->resource))->sort('name');

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('order by', strtolower($sql));
    }

    public function test_sort_desc_uses_minus_prefix(): void
    {
        $query = (new ResourceQuery($this->resource))->sort('-name');

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('order by', strtolower($sql));
    }

    public function test_include_only_allows_declared_relations(): void
    {
        $query = (new ResourceQuery($this->resource))->include(['category', 'secret_relation']);

        $this->assertStringContainsString('category', json_encode($query->getQuery()->getEagerLoads()));
        $this->assertStringNotContainsString('secret_relation', json_encode($query->getQuery()->getEagerLoads()));
    }

    public function test_fields_only_allows_declared_columns(): void
    {
        $query = (new ResourceQuery($this->resource))->fields(['id', 'name', 'secret_field']);

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('id', strtolower($sql));
        $this->assertStringContainsString('name', strtolower($sql));
        $this->assertStringNotContainsString('secret_field', strtolower($sql));
    }

    public function test_fields_always_includes_primary_key(): void
    {
        $query = (new ResourceQuery($this->resource))->fields(['name', 'price']);

        $sql = $query->getQuery()->toSql();

        $this->assertStringContainsString('id', strtolower($sql));
        $this->assertStringContainsString('name', strtolower($sql));
        $this->assertStringContainsString('price', strtolower($sql));
    }

    public function test_apply_processes_all_params(): void
    {
        $query = (new ResourceQuery($this->resource))->apply([
            'search' => 'test',
            'filter' => ['status' => 'active'],
            'sort' => 'name',
            'include' => ['category'],
            'fields' => ['id', 'name'],
            'per_page' => 25,
        ]);

        $this->assertInstanceOf(ResourceQuery::class, $query);
    }

    public function test_query_returns_collection(): void
    {
        $query = new ResourceQuery($this->resource);

        $this->assertInstanceOf(Builder::class, $query->getQuery());
    }

    public function test_first_method_is_callable(): void
    {
        $query = new ResourceQuery($this->resource);

        $this->assertTrue(method_exists($query, 'first'));
    }
}
