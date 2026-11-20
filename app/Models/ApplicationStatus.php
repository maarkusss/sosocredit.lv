<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property $id
 * @property $name
 * @property $multiplier
 */

class ApplicationStatus extends Model
{
    protected $guarded = [];
    protected $table = 'application_status';

    public static function byName(string $name): ApplicationStatus
    {
        try {
            $status =  static::where('name', $name)->first();
            if (!$status) throw new Exception('Application status not found');
            return $status;
        } catch (Exception $e) {
            //Log::error("ApplicationStatus " . $name . " :", [$e->getMessage()]);
        }

        if( ! empty($name)) { // allow empty statuses as for new export system
            Log::warning("Incorrect Application Status: " . $name . ". Revert to pending.");
        }

        return static::where('name', 'Pending')->firstOrFail(); // allow failing for fallback, db should have this status by default
    }
}
