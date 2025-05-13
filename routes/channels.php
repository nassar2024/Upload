<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('uploads.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});