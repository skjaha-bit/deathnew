<?php

namespace Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\EmergencyAccess\Models\EmergencyAccessRequest;

class EmergencyAccessRequested extends Notification
{
    use Queueable;

    public function __construct(protected EmergencyAccessRequest $emergencyAccessRequest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Emergency access requested',
            'message' => $this->emergencyAccessRequest->requester->name.' requested emergency access to '.$this->emergencyAccessRequest->targetUser->name."'s vault.",
            'emergency_access_request_id' => $this->emergencyAccessRequest->id,
        ];
    }
}
