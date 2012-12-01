<?php
ini_set( 'post_max_size', '32M' );
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
// HTTP
define('HTTP_SERVER', 'http://lider-market.lc/');
define('HTTP_IMAGE', 'http://lider-market.ru/image/');
define('HTTP_ADMIN', 'http://lider-market.lc/admin/');

// HTTPS
define('HTTPS_SERVER', 'http://lider-market.lc/');
define('HTTPS_IMAGE', 'http://lider-market.ru/image/');

// DIR
define('DIR_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('DIR_APPLICATION', DIR_ROOT . 'catalog/');
define('DIR_SYSTEM', DIR_ROOT . 'system/');
define('DIR_DATABASE', DIR_ROOT . 'system/database/');
define('DIR_LANGUAGE', DIR_ROOT . 'catalog/language/');
define('DIR_TEMPLATE', DIR_ROOT . 'catalog/view/theme/');
define('DIR_CONFIG', DIR_ROOT . 'system/config/');
define('DIR_IMAGE', DIR_ROOT . 'image/');
define('DIR_CACHE', DIR_ROOT . 'system/cache/');
define('DIR_DOWNLOAD', DIR_ROOT . 'download/');
define('DIR_LOGS', DIR_ROOT . 'system/logs/');

// DB
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'mysql');
define('DB_DATABASE', 'fl_lidermarket');
define('DB_PREFIX', '');
?>