<?php

if (!function_exists('ar_fix')) {
    function ar_fix($text) {
        return \App\Helpers\ArabicReshaper::fix($text);
    }
}
