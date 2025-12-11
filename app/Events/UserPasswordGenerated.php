<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserPasswordGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $password;
    public string $userType; // 'doctor', 'patient', 'frontdesk'

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $password, string $userType)
    {
        $this->user = $user;
        $this->password = $password;
        $this->userType = $userType;
    }
}
