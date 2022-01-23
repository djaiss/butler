<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'author_id',
        'author_name',
        'title',
        'body',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = [
            'id' => $this->id,
            'vault_id' => $this->contact->vault_id,
            'contact_id' => $this->contact->id,
            'title' => $this->title,
            'body' => $this->body,
        ];

        return $array;
    }

    /**
     * Get the contact associated with the note.
     *
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the author associated with the note.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the note's feed item.
     */
    public function feedItem()
    {
        return $this->morphOne(ContactFeedItem::class, 'feedable');
    }
}
