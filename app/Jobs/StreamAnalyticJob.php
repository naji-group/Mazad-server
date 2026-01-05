<?php

namespace App\Jobs;

use App\Models\LiveComment;
use App\Models\LiveStream;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StreamAnalyticJob implements ShouldQueue
{
    use Queueable;
    public  LiveStream $livestream;
    /**
     * Create a new job instance.
     */
    public function __construct(LiveStream $livestream)
    {
        $this->livestream=$livestream;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
   $count= LiveComment::where('live_stream_id',$this->livestream->id)->count();
   $this->livestream->comments_count=$count;
   $this->livestream->save;
    }
}
