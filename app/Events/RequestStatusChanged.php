<?php

namespace App\Events;

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Enums\RequestStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProcurementRequest $request;
    public RequestStatus $oldStatus;
    public RequestStatus $newStatus;
    public User $changedBy;
    public ?string $comments;

    /**
     * Create a new event instance.
     */
    public function __construct(
        ProcurementRequest $request,
        RequestStatus $oldStatus,
        RequestStatus $newStatus,
        User $changedBy,
        ?string $comments = null
    ) {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
        $this->comments = $comments;
    }
}
