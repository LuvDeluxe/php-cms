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
  } catch (PDOException $e) {
    $pdo->rollBack();
    throw $e;
  }
}
?>

<?php include '../includes/admin-header.php'; ?>
<main class="container admin" id="content">
    <form action="article-delete.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Delete Article</h1>
        <p>Click confirm to delete the article <em><?= html_escape($article['title']) ?></em></p>
        <input type="submit" name="delete" value="Confirm" class="btn btn-primary">
        <a href="articles.php" class="btn btn-danger">Cancel</a>
    </form>
</main>
<?php include '../includes/admin-footer.php'; ?>
