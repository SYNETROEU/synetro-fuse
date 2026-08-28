<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests\Resources\Stubs;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
}
