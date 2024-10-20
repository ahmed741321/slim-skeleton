<?php

if (!defined('DS')) {
    define("DS", DIRECTORY_SEPARATOR);
}
if (!defined('APP_PATH')) {
    define("APP_PATH", realpath(dirname(__FILE__)));
}
if (!defined('API_URL')) {
    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
    define("API_URL", $scheme . "://" . $_SERVER['HTTP_HOST'] . "/");
}
if (!defined('Excluded_Route')) {
    define("Excluded_Route",  []);
}
if (!defined('Get_Routes_With_Token')) {
    define("Get_Routes_With_Token",  []);
}

require APP_PATH . DS . "vendor" . DS . "autoload.php";
