<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $message,
        private readonly string $status,
        private readonly string $category
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'status' => $this->status,
            'category' => $this->category,
        ];
    }
}
