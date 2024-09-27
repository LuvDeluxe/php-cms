<?php
function html_escape($text): string
{
  $text = $text ?? '';

  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
}

function format_date(string $string): string
{
  $date = date_create_from_format('Y-m-d H:i:s', $string);
  return $date->format('F,d,y');
}

function redirect(string $location, array $parameters = [], $response_code = 302)
{
  $qs = $parameters ? '?' . http_build_query($parameters) : '';
  $location .= $qs;
  header('Location: ' . DOC_ROOT . $location, true, $response_code);
  exit;
}

function create_filename(string $filename, string $uploads): string
{
  $basename = pathinfo($filename, PATHINFO_FILENAME);
  $extension = pathinfo($filename, PATHINFO_EXTENSION);
  $cleanname = preg_replace("/[^A-z0-9]/", "-", $basename);
  $filename = $cleanname . '.' . $extension;
  $i = 0;

  while (file_exists($uploads . $filename)) {
    ++$i;
    $filename = $basename . $i . '.' . $extension;
  }
  return $filename;
}

function handle_error($error_type, $error_message, $error_file, $error_line)
{
  throw new ErrorException($error_message, 0, $error_type, $error_file, $error_line);
}

function handle_shutdown()
{
  $error = error_get_last();
  if ($error !== null) {
    $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
    handle_exception($e);
  }
}

