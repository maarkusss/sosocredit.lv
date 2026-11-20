<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $application_id
 * @property $link
 * @property $requisition_id
 * @property $agreement_id
 * @property $completed_at
 * @property $created_at
 * @property $updated_at
 */
class ApplicationGocardlessSession extends Model
{
    use HasFactory;

    protected $table = 'applications__gocardless_sessions';

    protected $guarded = [];

    public $timestamps = true;

    public function complete(): ApplicationGocardlessSession
    {
        if (empty($this->completed_at)) {
            $this->completed_at = now();
            $this->save();
        }

        return $this;
    }

    public function isCompleted(): bool
    {
        return !empty($this->completed_at);
    }
}
