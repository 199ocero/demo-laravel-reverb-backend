<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('filachat.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});
