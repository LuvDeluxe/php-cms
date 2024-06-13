<?php
declare(strict_types=1);
include '../includes/database-connection.php';
include '../includes/functions.php';
include '../includes/validate.php';
$uploads = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$file_types = ['image/jpeg', 'image/png', 'image/gif',];
$file_ext = ['jpg', 'jpeg', 'png', 'gif',];
$max_size = 5242800;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$temp = $_FILES['image']['tmp_name'] ?? '';
$destination = '';

$article = [
  'id' => $id,
  'summary' => '',
  'member_id' => 0,
  'image_id' => null,
  'image_file' => '',
  'title' => '',
  'content' => '',
  'category_id' => 0,
  'published' => false,
  'image_alt' => '',
];
$errors = [
  'warning' => '',
  'author' => '',
  'title' => '',
  'category' => '',
  'summary' => '',
  'image_file' => '',
  'content' => '',
  'image_alt' => '',
];

if ($id) {
  $sql = "SELECT a.id, a.title, a.summary, a.content,
          a.category_id, a.member_id, a.image_id, a.published,
          i.file AS image_file,
          i.alt AS image_alt
          FROM article AS a
          LEFT JOIN image AS i ON a.image_id = i.id
          WHERE a.id = :id;";
  $article = pdo($pdo, $sql, [$id])->fetch();
  if (!$article) {
    redirect('articles.php', ['failure' => 'Article not found']);
  }
}

$saved_image = $article['image_file'] ? true : false;

$sql = "SELECT id, forename, surname FROM member;";
$authors = pdo($pdo, $sql)->fetchAll();
$sql = "SELECT id, name FROM category;";
$categories = pdo($pdo, $sql)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $errors['image_file'] == ($_FILES['image']['error'] === 1) ? 'File too big ' : '';

  if ($temp && $_FILES['image']['error'] === 0) {
    $article['image_alt'] = $_POST['image_alt'];
  }
  $errors['image_file'] .= in_array(mime_content_type($temp), $file_types) ? '' : 'Wrong file type. ';
  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $errors['image_file'] .= in_array($ext, $file_ext) ? '' : 'Wrong file extension.';
  $errors['image_file'] .= ($_FILES['image']['size'] <= $max_size) ? '' : 'File too big. ';
  $errors['image_alt'] = (is_text($article['image_alt'], 1, 254)) ? '' : 'Alt text must be 1-254 characters.';

  if ($errors['image_file'] === '' && $errors['image_alt'] === '') {
    $article['image_file'] = create_filename($_FILES['image']['name'], $uploads);
    $destination = $uploads . $article['image_file'];
  }
}