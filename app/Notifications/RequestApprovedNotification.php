<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Mail\RequestApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProcurementRequest $request,
        public ?string $comments = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable)
    {
        return new RequestApprovedMail($this->request, $this->comments);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_approved',
            'title' => 'Request Approved',
            'message' => "Request {$this->request->request_number} for {$this->request->project->name} has been approved",
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'project_name' => $this->request->project->name,
            'comments' => $this->comments,
            'url' => route('requests.show', $this->request),
        ];
    }
}
