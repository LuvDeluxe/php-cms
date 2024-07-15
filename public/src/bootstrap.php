<?php
define('APP_ROOT', dirname(__FILE__, 2));
require APP_ROOT . '/includes/functions.php';
require APP_ROOT . '/config/config.php';

if (DEV === false) {
  set_exception_handler('handle_exception');
  set_error_handler('handle_error');
  register_shutdown_function('handle_shutdown');
}

spl_autoload_register(function($class)
{
  $path = APP_ROOT . '/src/classes/';
  require $path . $class . '.php';
});

$cms = new CMS($dsn, $username, $password);
unset($dsn, $username, $password);