<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

// db options
define('DB_NAME', '@@DBNAME@@');
define('DB_USER', '@@DBUN@@');
define('DB_PASSWORD', '@@DBPASS@@');
define('DB_HOST', 'localhost');
define('DB_TABLE', '@@DBTABLE@@');

// connect to database
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// change to limit short url creation to a single IP
$allowed_ips = serialize(array('97.100.116.77'));
define('LIMIT_TO_IP', $allowed_ips);
//Is the current IP authorized?
if(in_array($_SERVER['REMOTE_ADDR'], unserialize(LIMIT_TO_IP))) $auth = true;
else $auth = false;
define('AUTH',$auth);

// change to TRUE to start tracking referrals
define('TRACK', TRUE);

// check if URL exists first
define('CHECK_URL', TRUE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', TRUE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');