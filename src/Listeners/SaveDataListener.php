<?php

namespace Smbear\Avatax\Listeners;

use Illuminate\Support\Facades\Log;
use Smbear\Avatax\Events\SaveDataEvent;
use Smbear\Avatax\Models\AvataxRecords;

class SaveDataListener
{
    public function handle(SaveDataEvent $event)
    {
        try {
            AvataxRecords::create($event->data);
        }catch (\Exception $exception){
            Log::channel(config('avatax.channel'))
                ->info('数据库记录异常'.json_encode($event->data).'异常原因：'.$exception->getMessage());
        }
    }
}