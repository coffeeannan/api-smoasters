<?php

namespace App\Handler;

use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

//The class extends "ProcessWebhookJob" class as that is the class 
//that will handle the job of processing our webhook before we have 
//access to it.

class ProcessWebhook extends ProcessWebhookJob
{
    public function handle()
    {
        $event = json_decode($this->webhookCall, true);
        $data = $event['body'];
        var_dump($data);
        //Acknowledge you received the response
        http_response_code(200);
    }
}
