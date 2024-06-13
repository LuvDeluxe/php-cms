<?php
// DB function
function pdo(PDO $pdo, string $sql, array $arguments = null) {
  if (!$arguments) {
    return $pdo->query($sql); // run sql & return PDO statement obj
  }
  $statement = $pdo->prepare($sql);
  $statement->execute($arguments);
  return $statement;
}

// Formating
function html_escape($text): string {
  $text = $text ?? '';

  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
}

function format_date(string $string): string {
  $date = date_create_from_format('Y-m-d H:i:s', $string);

  return $date->format('F d, Y');
}

// Error and exception handling

set_error_handler('handle_exception');
function handle_exception($e) {
  error_log($e);
  http_response_code(500);
  echo "<h1>Sorry, a problem occured
            The site's owners have been informed. Please try again later !</h1>";
}

register_shutdown_function('handle_shutdown');
function handle_shutdown() {
  $error = error_get_last();

  if($error !== null) {
    $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
    handle_exception($e);
  }
}
function redirect(string $location, array $parameters = [], $response_code = 302) {
  $qs = $parameters ? '?' . http_build_query($parameters) : '';
  $location = $location . $qs;
  header('Location: ' . $location, $response_code);
  exit;
}

// create file name
function create_filename(string $filename, string $uploads) : string
{
  $basename = pathinfo($filename, PATHINFO_FILENAME);
  $extension = pathinfo($filename, PATHINFO_EXTENSION);
  $cleanname = preg_replace("/[^A-z0-9]/", "-", $basename);
  $filename = $cleanname . '.' . $extension;
  $i = 0;
  while (file_exists($uploads . $filename)) {
    $i = $i + 1;
    $filename = $basename . $i . '.' . $extension;
  }
  return $filename;
}