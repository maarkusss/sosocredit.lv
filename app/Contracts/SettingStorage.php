<?php

namespace App\Contracts;

interface SettingStorage
{
    /**
     * Fetches a value from the Settings storage.
     */
    public function get(string $key);
}
