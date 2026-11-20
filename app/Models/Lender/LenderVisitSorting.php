<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $id
 * @property $visit_id
 * @property $lender_id
 * @property $position
 * @property $bot
 */
class LenderVisitSorting extends Model
{
    use BelongsToLender;

    protected $guarded = [];
    protected $table = 'lenders__visits_sorting';

    public function visit(): BelongsTo
    {
        return $this->belongsTo(LenderVisit::class, 'visit_id')->where('bot', 0);
    }
}
