<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('active-evacuees', function () {
    return true;
});

Broadcast::channel('incident-report', function () {
    return true;
});

Broadcast::channel('emergency-report', function () {
    return true;
});

Broadcast::channel('area-report', function () {
    return true;
});

Broadcast::channel('evacuation-center-locator', function () {
    return true;
});

Broadcast::channel('notification', function () {
    return true;
});
