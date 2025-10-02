<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('is_impersonating')) {
    function is_impersonating(): bool
    {
        return Session::has('impersonator_id');
    }
}
