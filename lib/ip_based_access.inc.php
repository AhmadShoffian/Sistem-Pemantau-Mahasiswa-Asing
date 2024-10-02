<?php
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

if (!function_exists('do_checkIP')) {
    function do_checkIP ($module = 'general')
    {
        global $sysconf;
        if (isset($sysconf['ipaccess'][''.$module.''])) {
            $accmod = $sysconf['ipaccess'][''.$module.''];
        } else {
            die ('Invalid access module');
        }
        $is_allowed = false;
        $remote_addr = $_SERVER['REMOTE_ADDR'];
        if (($accmod != 'all') AND (is_array($accmod))) {
            foreach ($accmod as $value) {
                $pattern = "/^".$value."/i";
                if (preg_match($pattern, $remote_addr)) {
                    $is_allowed = true;
                }
            }
        } elseif ($accmod == 'all') {
            $is_allowed = true;
        } else {
            $is_allowed = false;
        }
        if (!$is_allowed) {
            echo 'Stop here! Access now allowed.';
            exit();
        }
    }
}

do_checkIP();
