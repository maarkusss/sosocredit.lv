<?php

namespace App\Models;

use App\Services\GeoIp;
use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $clicks
 * @property $user_agent
 * @property $ip
 * @property $country
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $gclid
 * @property $fbclid
 * @property $from
 * @property $to
 * @property $created_at
 * @property $updated_at
 */
class Visitor extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'visitors';

    protected static function newFactory(): Factory
    {
        return VisitorFactory::new();
    }

    public function fillCountry(): void
    {
        if (!empty($this->country)) {
            return;
        }

        if (empty($this->ip)) {
            return;
        }

        $geo_ip = app(GeoIp::class);
        /** @var GeoIp $geo_ip  */
        $this->country = $geo_ip->getCountryCodeByIp($this->ip);
        $this->save();
    }
}
