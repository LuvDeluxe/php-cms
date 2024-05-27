<?php
declare(strict_types=1);
include '../includes/database-connection.php';
include '../includes/functions.php';
include '../includes/validate.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = [
  'id' => $id,
  'name' => '',
  'description' => '',
  'navigation' => false
];
$errors = [
  'warning' => '',
  'name' => '',
  'description' => '',
];
