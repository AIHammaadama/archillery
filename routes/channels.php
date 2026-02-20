<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users-chat', function ($msg) {
    return $msg;
});
