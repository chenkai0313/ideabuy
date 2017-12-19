<?php

namespace App\Listeners;

use App\Events\UserInfo;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserInfo
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
     * @param  UserInfo  $event
     * @return void
     */
    public function handle(UserInfo $event)
    {
        //
        $data=$event->params;

    }
}
