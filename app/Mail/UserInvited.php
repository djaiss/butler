<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvited extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $invitedUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, User $invitedUser)
    {
        $this->user = $user;
        $this->invitedUser = $invitedUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invitationRoute = route('invitation.show', [
            'code' => $this->invitedUser->invitation_code,
        ]);

        return $this->view('view.name')
            ->with('userName', $this->user->name)
            ->with('url', $invitationRoute);
    }
}
