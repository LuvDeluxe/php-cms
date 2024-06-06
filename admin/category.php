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

if ($id) {
  $sql = "SELECT id, name, description, navigation
          FROM category
          WHERE id = :id;";
  $category = pdo($pdo, $sql, [$id])->fetch();
  if (!$category) {
    redirect('categories.php', ['failure' => 'Category not found']);
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $category['name'] = $_POST['name'];
  $category['description'] = $_POST['description'];
  $category['navigation'] = (isset($_POST['navigation']) and ($_POST['navigation'] === 1)) ? 1 : 0;

  $errors['name'] = (is_text($category['name'], 1, 24))
    ? '' : 'Name should be 1-24 characters.';
  $errors['description'] = (is_text($category['description'], 1, 254))
    ? '' : 'Description should be 1-254 characters.';
  $invalid = implode($errors);

  if ($invalid) {
    $errors['warning'] = 'Please correct errors';
  } else {
    $arguments = $category;
    if ($id) {

    } else {

    }
  }

}

