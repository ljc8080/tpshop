<?php
if (!function_exists('ellipsis')) {
    function ellipsis($str, $length=null)
    {
        $length = $length ?? 20;
        if (strlen($str) > $length) {
            return mb_substr($str, 0, $length) . '...';
        } else {
            return $str;
        }
    }
}
