<?php

declare(strict_types=1);

namespace Synetro\Fuse\Audit\Events;

use Synetro\Fuse\Audit\Audit;

class AuditRecorded
{
    public function __construct(public Audit $audit) {}
}
