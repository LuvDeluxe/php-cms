<?php
declare(strict_types=1);
require_once '../includes/database-connection.php';
require_once '../includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
  redirect('articles.php', ['failure' => 'Article not found']);
}
$article = false;
$sql = "SELECT a.title, a.image_id,
        i.file AS image_file
        FROM article AS a
        LEFT JOIN image AS i ON a.image_id = i.id
        WHERE a.id = :id;";
$article = pdo($pdo, $sql, [$id])->fetch();

if (!$article) {
  redirect('articles.php', ['failure' => 'Article not found']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $pdo->beginTransaction();

    if ($article['image_id']) {
      $sql = "UPDATE article SET image_id = null WHERE id = :article_id;";
      pdo($pdo, $sql, [$id]);
      $sql = "DELETE FROM image WHERE id = :id";
      pdo($pdo, $sql, [$article['image_id']]);
      $path = '../uploads/' . $article['image_file'];
      if (file_exists($path)) {
        $unlink = unlink($path);
      }
    }

    $sql = "DELETE FROM article WHERE id = :id;";
    pdo($pdo, $sql, [$id]);
    $pdo->commit();
    redirect('articles.php', ['success' => 'Article deleted']);
  } catch (PDOException $e){
    $pdo->rollBack();
    throw $e;
  }
}