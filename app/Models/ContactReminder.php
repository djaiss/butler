<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactReminder extends Model
{
    use HasFactory;

    protected $table = 'contact_reminders';

    /**
     * Possible type.
     */
    const TYPE_ONE_TIME = 'one_time';
    const TYPE_RECURRING_DAY = 'recurring_day';
    const TYPE_RECURRING_MONTH = 'recurring_month';
    const TYPE_RECURRING_YEAR = 'recurring_year';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'label',
        'day',
        'month',
        'year',
        'type',
        'frequency_number',
        'last_triggered_at',
        'number_times_triggered',
    ];

    /**
     * Get the contact associated with the contact reminder.
     *
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user records associated with the contact reminder.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('scheduled_at', 'triggered');
    }

    /**
     * Get the scheduled reminders associated with the contact reminder.
     *
     * @return HasMany
     */
    public function scheduledContactReminders()
    {
        return $this->hasMany(ScheduledContactReminder::class);
    }
}
