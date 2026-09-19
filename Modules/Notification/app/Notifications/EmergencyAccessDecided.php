<?php

namespace Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\EmergencyAccess\Models\EmergencyAccessRequest;

class EmergencyAccessDecided extends Notification
{
    use Queueable;

    public function __construct(protected EmergencyAccessRequest $emergencyAccessRequest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $decision = $this->emergencyAccessRequest->status;

        return [
            'title' => 'Emergency access '.$decision,
            'message' => 'Your request for '.$this->emergencyAccessRequest->targetUser->name."'s vault was {$decision}.".
                ($this->emergencyAccessRequest->decision_reason ? ' Reason: '.$this->emergencyAccessRequest->decision_reason : ''),
            'emergency_access_request_id' => $this->emergencyAccessRequest->id,
        ];
    }
}
