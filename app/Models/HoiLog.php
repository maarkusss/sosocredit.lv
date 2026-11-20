<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property $id
 * @property $type
 * @property $to
 * @property $subject
 * @property $htmllink
 * @property $created_at
 */
class HoiLog extends Model
{
    use HasFactory;

    protected $table = 'hoi__logs';
    protected $guarded = [];
    public $timestamps = true;
    public const UPDATED_AT = null;

    public static function logMail($to, $subject, $htmllink)
    {
        try {
            HoiLog::query()
                ->insert([
                    'type' => 'mail',
                    'to' => $to,
                    'subject' => $subject,
                    'htmllink' => $htmllink,
                    'created_at' => now()->format('Y-m-d H:i:00'),
                ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public static function prune(DateTimeInterface $before): int
    {
        $chunk_size = 1000;

        $query = DB::table('hoi__logs')
            ->where('created_at', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take($chunk_size)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }
}
