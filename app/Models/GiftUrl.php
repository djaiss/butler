<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftUrl extends Model
{
    use HasFactory;

    protected $table = 'gift_occasions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gift_id',
        'url',
    ];

    /**
     * Get the gift associated with the gift url.
     *
     * @return BelongsTo
     */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }
}
