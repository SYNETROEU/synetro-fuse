<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Illuminate\Support\Facades\DB;
use Synetro\Fuse\Bulk\BulkManager;

class BulkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--force' => true]);
    }

    public function test_bulk_update_returns_affected_rows(): void
    {
        $query = DB::table('fuse_features');
        $bulk = new BulkManager($query);

        $this->assertIsInt($bulk->update(['enabled' => true]));
    }

    public function test_bulk_delete_returns_affected_rows(): void
    {
        $query = DB::table('fuse_features');
        $bulk = new BulkManager($query);

        $this->assertIsInt($bulk->delete());
    }
}
