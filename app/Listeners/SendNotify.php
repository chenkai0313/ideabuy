<?php

namespace App\Listeners;

use App\Events\NotifyPosh;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotify
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotifyPosh  $event
     * @return void
     */
    public function handle(NotifyPosh $event)
    {
        $data=$event->params;
        $result=\message::sendMessage($data);
        if($result['code']!=1){
            \Log::useFiles(storage_path().'/logs/message-'.date('Y-m-d').'.log','warning');
            \Log::warning('消息推送错误：',$result);
        }
    }
}
