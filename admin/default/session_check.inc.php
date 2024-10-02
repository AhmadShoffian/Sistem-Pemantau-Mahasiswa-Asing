<?php
// be sure that this file not accessed directly
if (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

// check session
$unauthorized = !isset($_SESSION['uid']) && !isset($_SESSION['uname']) && !isset($_SESSION['realname']);
if ($unauthorized) {
    $msg = '<script type="text/javascript">'."\n";
    $msg .= 'alert(\''.('You are not authorized to view this section').'\');'."\n";
    $msg .= 'top.location.href = \''.SWB.'index.php?p=login\';'."\n";
    $msg .= '</script>'."\n";
    // unset cookie admin flag
    setcookie('admin_logged_in', false, time()-86400, SWB);
    simbio_security::destroySessionCookie($msg, COOKIES_NAME, SWB.'admin', true);
}

// checking session checksum
$server_addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : (isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : gethostbyname($_SERVER['SERVER_NAME']));
$unauthorized = $_SESSION['checksum'] != md5($server_addr.SB.'admin');
if ($unauthorized) {
    $msg = '<div style="padding: 5px; border: 1px dotted #FF0000; color: #FF0000;">';
    $msg .= ('You are not authorized to view this section');
    $msg .= '</div>'."\n";
    // unset cookie admin flag
    setcookie('admin_logged_in', true, time()-86400, SWB);
    simbio_security::destroySessionCookie($msg, COOKIES_NAME, SWB.'admin', true);
}

// check for session timeout
$curr_timestamp = time();
$timeout = ($curr_timestamp-$_SESSION['logintime']) >= $sysconf['session_timeout'];
if ($timeout) {
    $msg = '<div style="padding: 5px; border: 1px dotted #FF0000; color: #FF0000;">';
    $msg .= ('Your Login Session has already timeout!').' <a target="_top" href="'.SWB.'index.php?p=login">Re-Login</a>';
    $msg .= '</div>'."\n";
    // unset cookie admin flag
    setcookie('admin_logged_in', true, time()-86400, SWB);
    simbio_security::destroySessionCookie($msg, COOKIES_NAME, SWB.'admin', true);
} else {
    // renew session logintime
    $_SESSION['logintime'] = time();
}
