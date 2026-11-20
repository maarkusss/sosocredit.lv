<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $lender_id
 * @property $application_id
 * @property string $link
 * @property string $source
 * @property string $timestamp
 */

class ProviderLinkVisit extends Model
{
    protected $table = 'provider_link_visits';

    protected $fillable = [
        'lender_id',
        'application_id',
        'link',
        'source'
    ];

    public function provider(): BelongsTo
    {
        return $this->BelongsTo(Lender::class,'lender_id','id');
    }

    public function application(): BelongsTo
    {
        return $this->BelongsTo(Application::class,'application_id','id');
    }
}
