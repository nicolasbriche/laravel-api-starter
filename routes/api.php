<?php

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});