<?php

namespace App\Jobs\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledContactReminder;
use App\Notifications\SendEmailReminder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ScheduledContactReminder $scheduledReminder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ScheduledContactReminder $scheduledReminder)
    {
        $this->scheduledReminder = $scheduledReminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailAddress = $this->scheduledReminder->userNotificationChannel->content;

        Notification::route('mail', $emailAddress)
            ->notify(new SendEmailReminder($this->scheduledReminder));
    }
}