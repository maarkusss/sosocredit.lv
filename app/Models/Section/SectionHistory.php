<?php

namespace App\Models\Section;

use App\Models\Section;
use App\Models\User\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class SectionHistory extends Model
{
    use BelongsToUser;

    protected $guarded = [];
    protected $table = 'sections__history';

    protected $casts = [
        'json' => 'array',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function h1_tag()
    {
        return $this->section->h1_tag();
    }

    public function canonical_url()
    {
        return $this->section->canonical_url();
    }
}
