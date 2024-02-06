<?php

namespace TechStudio\Lms\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $videoId;
    public $tries = 10;
    public $backoff = [60, 60, 2*60, 3*60, 5*60, 10*60, 15*60, 20*60, 22*60, 25*60];
    /**
     * Create a new job instance.
     */
    public function __construct($lesson, $videoId)
    {
        $this->record = $lesson;
        $this->videoId = $videoId;
        $this->onQueue('process-video');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("video process");
        $api_key = env('ARVAN_API_KEY');
        $endpoint = "https://napi.arvancloud.ir/vod/2.0/videos/";
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $endpoint . $this->videoId, ['headers' => [
            'Authorization' => $api_key,
        ]]);
        if ($response->getStatusCode() == 200) {
            $content = $response->getBody();
            $content = json_decode($content)->data;
            $status = false;
            foreach ($content as $i => $data) {
                if ($i == 'status' && $data == 'complete') {
                    $status = true;
                }
                if ($i == 'player_url') {
                    $player = $data;
                }
            }
        }

        if ($status) {
            $this->record->content = json_decode(str_replace(
                $this->videoId,
                $player,
                json_encode($this->record->content)
            ));
            $this->record->save();
            $this->dontRelease();
        }
        $this->realese();
    }
}
