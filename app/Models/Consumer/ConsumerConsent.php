<?php

namespace App\Models\Consumer;

use App\Models\Consumer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * @property int $id
 * @property int $consumer_id
 * @property bool $terms
 * @property bool $marketing
 * @property string $user_agent
 * @property string $ip
 * @property Carbon $consented_at
 * @property string $terms_snapshot
 * @property string $marketing_snapshot
 * @property $created_at
 * @property $updated_at
 *
 * @property Consumer $consumer
 */
class ConsumerConsent extends Model
{
    use BelongsToConsumer;

    protected $guarded = [];
    protected $table = 'consumers__consents';
    protected $casts = [
        'terms' => 'boolean',
        'marketing' => 'boolean',
        'consented_at' => 'datetime'
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Consent records are immutable. Create a new record instead.');
        });
    }

    public function scopeForConsumer(Builder $query, int $consumerId): Builder
    {
        return $query->where('consumer_id', $consumerId);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('consented_at')->orderByDesc('id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConsumerId(): int
    {
        return $this->consumer_id;
    }

    public function getConsumer(): Consumer
    {
        return $this->consumer;
    }

    public function hasAcceptedTerms(): bool
    {
        return $this->terms;
    }

    public function hasAcceptedMarketing(): bool
    {
        return $this->marketing;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getConsentedAt(): ?Carbon
    {
        return $this->consented_at;
    }

    public function getTermsSnapshot(): ?string
    {
        return $this->terms_snapshot;
    }

    public function getMarketingSnapshot(): ?string
    {
        return $this->marketing_snapshot;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

}
