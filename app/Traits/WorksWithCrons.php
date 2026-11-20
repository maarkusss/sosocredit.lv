<?php

namespace App\Traits;

use App\Models\Cron;

trait WorksWithCrons
{
    public function isCronEnabled(string $signature): bool
    {
        $cron = Cron::query()->where('signature', $signature)->select(['id', 'active'])->first();
        /** @var Cron $cron */

        return !empty($cron->id) && !empty($cron->active);
    }
}
