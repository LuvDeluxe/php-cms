<?php
/**
 * This script manages the deletion of an image associated with an article in the CMS.
 * It confirms the existence of the image, provides a confirmation form for deletion,
 * and handles the database and file system cleanup upon form submission.
 */

// Ensures strict typing for all functions
declare(strict_types=1);
// Load cms dependencies and configuration
include '../../src/bootstrap.php';

// Retrieve and validate the article ID from GET parameters
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$image = [];

// Redirects to article list if ID is invalid or missing.
if (!$id) {
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}
// Fetch article data by ID
$article = $cms->getArticle()->get($id, false);
// Redirects if article has no associated image
if (!$article['image_file']) {
  redirect('admin/article.php', ['id' => $id]);
}
// delete and redirect
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $path = APP_ROOT . '/public/uploads/' . $article['image_file'];
  $cms->getArticle()->imageDelete($article['image_id'], $path, $id);
  redirect('admin/article.php', ['id' => $id]);
}

?>

<?php include APP_ROOT . '/public/includes/admin-header.php'; ?>
<main class="container admin" id="content">
    <form action="image-delete.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Delete image</h1>
        <p><img src="../uploads/<?= html_escape($image['file'])?>" alt="<?= html_escape($image['alt']) ?>"></p>
        <p>Click confirm to delete the image: </p>
        <input type="submit" name="delete" value="Confirm" class="btn btn-primary">
        <a href="article.php?id=<?= $id ?>" class="btn btn-danger">Cancel</a>
    </form>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php'; ?>
