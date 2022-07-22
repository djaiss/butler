<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Scout\Searchable;

class Gift extends Model
{
    use HasFactory;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vault_id',
        'gift_occasion_id',
        'gift_state_id',
        'name',
        'description',
        'budget',
        'currency_id',
        'received_at',
        'given_at',
        'bought_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'received_at',
        'given_at',
        'bought_at',
    ];

    /**
     * Get the vault associated with the gift.
     *
     * @return BelongsTo
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the gift occasion associated with the gift.
     *
     * @return BelongsTo
     */
    public function giftOccasion(): BelongsTo
    {
        return $this->belongsTo(GiftOccasion::class);
    }

    /**
     * Get the gift state associated with the gift.
     *
     * @return BelongsTo
     */
    public function giftState(): BelongsTo
    {
        return $this->belongsTo(GiftState::class);
    }

    /**
     * Get the currency associated with the gift.
     *
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the contacts that made the gift.
     *
     * @return BelongsToMany
     */
    public function donators(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'gift_donators', 'gift_id', 'contact_id');
    }

    /**
     * Get the contact records the gift was made to.
     *
     * @return BelongsToMany
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'gift_recipients', 'gift_id', 'contact_id');
    }

    /**
     * Get the gift's feed item.
     */
    public function feedItem(): MorphOne
    {
        return $this->morphOne(ContactFeedItem::class, 'feedable');
    }

    /**
     * Get the gift urls associated with the gift.
     *
     * @return HasMany
     */
    public function giftUrls()
    {
        return $this->hasMany(GiftUrl::class);
    }
}
