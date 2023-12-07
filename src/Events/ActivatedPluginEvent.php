<?php

namespace Tec\PluginManagement\Events;

use Tec\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class ActivatedPluginEvent extends Event
{
    use SerializesModels;

    public function __construct(public string $plugin)
    {
    }
}
