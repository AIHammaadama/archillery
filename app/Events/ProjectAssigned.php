<?php

namespace App\Events;

use App\Models\Project;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project $project;
    public User $user;
    public string $roleType;

    /**
     * Create a new event instance.
     */
    public function __construct(Project $project, User $user, string $roleType)
    {
        $this->project = $project;
        $this->user = $user;
        $this->roleType = $roleType;
    }
}
