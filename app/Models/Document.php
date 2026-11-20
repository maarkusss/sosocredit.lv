<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $uuid
 * @property $token
 * @property $filename
 * @property $original_filename
 * @property $name
 * @property $personal_code
 * @property $downloads
 * @property $created_at
 * @property $updated_at
 *
 * @property $download_link
 */
class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    public $timestamps = true;

    protected array $encryptable = [
        'name',
        'personal_code',
    ];

    protected $casts = [
        'downloads' => 'array',
    ];

    public static $STORAGE_PATH = 'documents/';
    public static $MAX_DOWNLOADS = 2;

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable)) {
            $value = decrypt($value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getDownloadLinkAttribute(): string
    {
        return route('documents.download', [
            'uuid' => $this->uuid,
            'token' => $this->token,
        ]);
    }
}
