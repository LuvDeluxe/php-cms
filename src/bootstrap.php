<?php
/**
 * Root setup for the PHP CMS application
 * This script initializes the environment, loads necessary configurations,
 * and sets up error handling for production use.
 */

// Define the root directory of the application
define('APP_ROOT', dirname(__FILE__, 2));
// Load essential functions and configuration
require APP_ROOT . '/src/functions.php';
require APP_ROOT . '/config/config.php';

// Setup error handling for production environment
if (DEV === false) {
  // Custom handler for uncaught exceptions
  set_exception_handler('handle_exception');
  // Custom handler for errors
  set_error_handler('handle_error');
  // Function to catch fatal errors on shutdown
  register_shutdown_function('handle_shutdown');
}

/**
 * Autoload PHP classes from the classes directory
 * @param string $class The name of the class to load
 */
spl_autoload_register(function ($class) {
  $path = APP_ROOT . '/src/classes/';
  require $path . $class . '.php';
});

// Instantiate CMS with database connection details
// Note: Ensure these variables are defined in config.php
$cms = new CMS($dsn, $username, $password);
unset($dsn, $username, $password);