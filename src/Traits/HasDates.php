<?php

declare(strict_types=1);

namespace Synetro\Fuse\Traits;

use Illuminate\Support\Carbon;

trait HasDates
{
    public function human(): string
    {
        return $this->created_at?->diffForHumans() ?? '';
    }

    public function isOlderThan(int $amount, string $unit = 'days'): bool
    {
        return $this->created_at?->lt(Carbon::now()->sub($unit, $amount)) ?? false;
    }
}
