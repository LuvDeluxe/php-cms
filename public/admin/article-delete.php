<?php
declare(strict_types=1);
include '../../src/bootstrap.php';
$deleted = null;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}

$article = $cms->getArticle()->get($id, false);
if (!$article) {
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($article['image_id'])) {
    $path = APP_ROOT . '/public/uploads/' . $article['image_file'];
    $cms->getArticle()->imageDelete($article['image_id'], $path, $id);
  }
  $deleted = $cms->getArticle()->delete($id);
  if ($deleted === true) {
    redirect('admin/articles.php', ['success' => 'Article deleted']);
  } else {
    throw new Exception('Unable to delete article');
  }
}

?>

<?php include APP_ROOT . '/public/includes/admin-header.php' ?>
<main class="container admin" id="content">
    <form action="article-delete.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Delete Article</h1>
        <p>Click confirm to delete the article <em><?= html_escape($article['title']) ?></em></p>
        <input type="submit" name="delete" value="Confirm" class="btn btn-primary">
        <a href="articles.php" class="btn btn-danger">Cancel</a>
    </form>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php' ?>
