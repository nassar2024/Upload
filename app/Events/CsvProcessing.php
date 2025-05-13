<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CsvProcessing implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $upload;
    public $data;
    public $userId;

    public function __construct(Upload $upload, array $data, int $userId)
    {
        $this->upload = $upload;
        $this->data = $data;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('uploads.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'CsvProcessing';
    }

    public function broadcastWith()
    {
        return [
            'upload' => [
                'id' => $this->upload->id,
                'status' => $this->upload->status,
            ],
        ];
    }
}