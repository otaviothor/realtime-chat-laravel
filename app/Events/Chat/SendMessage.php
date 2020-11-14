<?php

namespace App\Events\Chat;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendMessage
 * @package App\Events\Chat
 */
class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Message
     */
    public $message;
    /**
     * @var int
     */
    public $userNotification;

    /**
     * Create a new event instance.
     *
     * @param Message $message
     * @param int $userNotificantion
     */
    public function __construct(Message $message, int $userNotificantion)
    {
        $this->message = $message;
        $this->userNotification = $userNotificantion;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("user.{$this->userNotification}");
    }

    public function broadcastAs()
    {
        return 'SendMessage';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
