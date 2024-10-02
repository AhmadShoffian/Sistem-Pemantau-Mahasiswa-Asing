<?php

// key to authenticate
define('INDEX_AUTH', '1');

/* Library Automation logout */

// required file
require '../sysconfig.inc.php';
// start the session
require SB.'admin/default/session.inc.php';

// write log
// redirecting pages
$msg = '<script type="text/javascript">';
if ($sysconf['logout_message']) {
    $msg .= 'alert(\''.('You Have Been Logged Out From Library Automation System').'\');';
}
$msg .= 'location.href = \''.SWB.'index.php\';';

// Disconnect Websocket
$msg .= 'Server = new FancyWebSocket("ws://'.$sysconf['chat_system']['server'].':'.$sysconf['chat_system']['server_port'].'");';
$msg .= 'Server.bind("close", function( data ) { log( "Disconnected." ); });';
$msg .= '</script>';

// unset admin cookie flag
setcookie('admin_logged_in', true, time()-86400, SWB);
// completely destroy session cookie
simbio_security::destroySessionCookie($msg, COOKIES_NAME, SWB.'admin/', true);