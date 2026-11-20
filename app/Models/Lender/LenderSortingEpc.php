<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;

class LenderSortingEpc extends Model
{
    use BelongsToLender;

    protected $guarded = [];
    protected $table = 'lenders__sorting_epc';
}
