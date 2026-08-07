<?php

namespace Hybridcore\Vote\Notifications;

use Hybridcore\Vote\Models\Vote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VoteConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Vote $vote) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'vote.confirmed',
            'points' => $this->vote->points_awarded,
            'message' => trans('vote::messages.notif_confirmed', ['points' => $this->vote->points_awarded]),
            'action_url' => route('vote.index'),
            'action_label' => trans('vote::messages.nav'),
        ];
    }
}
