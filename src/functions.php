<?php

/**
 * Escapes special characters in a string for safe HTML output.
 *
 * @param string|null $text The string to be escaped.
 * @return string The escaped string.
 */
function html_escape($text): string
{
  // Ensure $text is not null, default to an empty string if it is
  $text = $text ?? '';
  // Convert special characters to HTML entities
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Formats a date string into a readable format.
 *
 * @param string $string Date in 'Y-m-d H:i:s' format.
 * @return string Formatted date string in 'F,d,y' format.
 */
function format_date(string $string): string
{
  // Create a date object from the input string
  $date = date_create_from_format('Y-m-d H:i:s', $string);
  // Format and return the date
  return $date->format('F,d,y');
}

/**
 * Redirects the browser to a specified location with optional parameters.
 *
 * @param string $location The URL path to redirect to.
 * @param array $parameters Query parameters to append to the URL.
 * @param int $response_code HTTP response code for the redirect, defaults to 302.
 */
function redirect(string $location, array $parameters = [], $response_code = 302)
{
  // Build query string if parameters are provided
  $qs = $parameters ? '?' . http_build_query($parameters) : '';
  // Construct the full URL with parameters
  $location .= $qs;
  // Send the redirect header
  header('Location: ' . DOC_ROOT . $location, true, $response_code);
  // End script execution
  exit;
}

/**
 * Creates a unique filename by appending numbers if the file already exists.
 *
 * @param string $filename Original filename to be sanitized.
 * @param string $uploads Directory where the file will be stored.
 * @return string A unique filename.
 */
function create_filename(string $filename, string $uploads): string
{
  // Extract filename parts
  $basename = pathinfo($filename, PATHINFO_FILENAME);
  $extension = pathinfo($filename, PATHINFO_EXTENSION);
  // Clean the filename to remove special characters
  $cleanname = preg_replace("/[^A-z0-9]/", "-", $basename);
  // Initialize with cleaned name
  $filename = $cleanname . '.' . $extension;
  // Counter for uniqueness
  $i = 0;

  // Check if file exists and increment counter until a unique name is found
  while (file_exists($uploads . $filename)) {
    ++$i;
    $filename = $basename . $i . '.' . $extension;
  }
  return $filename;
}

/**
 * Converts PHP errors into exceptions for uniform error handling.
 *
 * @param int $error_type The type of error.
 * @param string $error_message The error message.
 * @param string $error_file The filename where the error occurred.
 * @param int $error_line The line number where the error occurred.
 */
function handle_error($error_type, $error_message, $error_file, $error_line)
{
  throw new ErrorException($error_message, 0, $error_type, $error_file, $error_line);
}

/**
 * Catches fatal errors on script shutdown.
 * This function is registered as a shutdown function to handle last-minute errors.
 */
function handle_shutdown()
{
  $error = error_get_last();
  if ($error !== null) {
    // Convert the fatal error into an ErrorException
    $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
    // Handle this exception like any other
    handle_exception($e);
  }
}

