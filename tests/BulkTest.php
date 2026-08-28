<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Illuminate\Database\Eloquent\Model;
use Synetro\Fuse\Tests\TestCase;

class BulkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--force' => true]);
    }

    public function test_bulk_update_returns_affected_rows(): void
    {
        $query = \Illuminate\Support\Facades\DB::table('fuse_features');
        $bulk = new \Synetro\Fuse\Bulk\BulkManager($query);

        $this->assertIsInt($bulk->update(['enabled' => true]));
    }

    public function test_bulk_delete_returns_affected_rows(): void
    {
        $query = \Illuminate\Support\Facades\DB::table('fuse_features');
        $bulk = new \Synetro\Fuse\Bulk\BulkManager($query);

        $this->assertIsInt($bulk->delete());
    }
}
