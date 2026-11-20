<?php

namespace App\Traits;

trait WorksWithLoadAvg
{
    public function isLoadAvgOver($limit): bool
    {
        if (function_exists('sys_getloadavg')) {
            $loadavg = sys_getloadavg();

            return !empty($loadavg[0]) && $loadavg[0] > $limit;
        }

        return false;
    }

    public function getLoadAvg()
    {
        if (function_exists('sys_getloadavg')) {
            $loadavg = sys_getloadavg();

            return $loadavg[0] ?? 0;
        }

        return -1;
    }
}
