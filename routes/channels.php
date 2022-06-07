<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat', function ($user) {
    return $user;
});

Broadcast::channel('new_referral', function ($referral) {
    return $referral;
});

Broadcast::channel('reco', function ($reco) {
    return $reco;
});

Broadcast::channel('referral_seen', function ($referral_seen) {
    return $referral_seen;
});

Broadcast::channel('referral_accepted', function ($referral_accepted) {
    return $referral_accepted;
});

Broadcast::channel('referral_rejected', function ($referral_rejected) {
    return $referral_rejected;
});

Broadcast::channel('referral_call', function ($referral_call) {
    return $referral_call;
});