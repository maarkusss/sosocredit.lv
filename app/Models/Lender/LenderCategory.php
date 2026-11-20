<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;

class LenderCategory extends Model
{
    use BelongsToLender;

    protected $guarded = [];
    protected $table = 'lenders__categories';
}
