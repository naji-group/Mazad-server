<?php

namespace App\Jobs;

use App\Models\Marketer;
use App\Notifications\MarketerNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMarketerNotification implements ShouldQueue
{
    use Queueable;
    public array $marketerIds;
    public string $title;
    public string $body;
    public array $data;
    public array $channels;
    /**
     * Create a new job instance.
     */
    public function __construct(array $marketerIds, string $title, string $body, array $data = [], array $channels = ['database', 'fcm'])
    {
        $this->marketerIds = $marketerIds;
        $this->title       = $title;
        $this->body        = $body;
        $this->data        = $data;
        $this->channels    = $channels;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $marketers = Marketer::whereIn('id', $this->marketerIds)->get();

        foreach ($marketers as $marketer) {
            $marketer->notify(new MarketerNotification(
                $this->title,
                $this->body,
                $this->data,
                $this->channels
            ));
        }
    }
}
