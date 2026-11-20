<?php

namespace App\Models\LoanType;

use App\Models\LoanType;
use App\Models\User\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTypeHistory extends Model
{
    use BelongsToUser;

    protected $guarded = [];
    protected $table = 'loan_types__history';

    public function loantype(): BelongsTo
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function canonical_url(): string
    {
        return $this->loantype->canonical_url();
    }
}
