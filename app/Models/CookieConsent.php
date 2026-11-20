<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Visitor;

class CookieConsent extends Model
{
    protected $fillable = [
        'visitor_id',
        'ip_address',
        'user_agent',
        'analytics',
        'marketing',
        'functional',
    ];

    protected $casts = [
        'analytics'    => 'boolean',
        'marketing'    => 'boolean',
        'functional'   => 'boolean',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }
}