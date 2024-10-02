<?php

 // key to authenticate
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}

require '../sysconfig.inc.php';
// set cookie
$cookie_path = preg_replace('@m\/*@i', '',SENAYAN_WEB_ROOT_DIR);
// create cookies of lightweight mode
if (isset($_GET['fullsite'])) {
    @setcookie('FULLSITE_MODE', 1, time()+43200, $cookie_path);
} else {
	// remove cookies
	@setcookie('FULLSITE_MODE', 0, time()-43200, $cookie_path);
}
// redirect to main bootstrap
header('Location: ../index.php');
?>
