<?php

// be sure that this file not accessed directly
if (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

// always use session cookies
@ini_set('session.use_cookies', true);
// use more secure session ids
@ini_set('session.hash_function', 1);
// no cache
@session_cache_limiter('nocache');
// set session name and start the session
@session_name(COOKIES_NAME);
// set session cookies params
@session_set_cookie_params(86400, SWB.'admin/');
// start session
session_start();
