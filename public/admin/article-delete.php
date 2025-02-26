<?php
/**
 * This script handles the deletion of an article from the CMS.
 * It verifies the article's existence, processes the deletion if requested via POST,
 * and provides a confirmation form for the user to confirm the deletion.
 */

// Ensures all functions require type declarations
declare(strict_types=1);
// Include necessary bootstrap file for CMS functionality
include '../../src/bootstrap.php';
// Variable to store deletion status
$deleted = null;
// Retrieve and validate the article ID from GET parameters
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  // Redirect if no valid ID is provided
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}
// Fetch article details using the CMS article handler
$article = $cms->getArticle()->get($id, false);
if (!$article) {
  // Redirect if article does not exist
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}
// Check if the form was submitted for deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // If an image is associated with the article, delete it first
  if (isset($article['image_id'])) {
    $path = APP_ROOT . '/public/uploads/' . $article['image_file'];
    $cms->getArticle()->imageDelete($article['image_id'], $path, $id);
  }
  // Attempt to delete the article
  $deleted = $cms->getArticle()->delete($id);
  if ($deleted === true) {
    // Redirect with success message if deletion was successful
    redirect('admin/articles.php', ['success' => 'Article deleted']);
  } else {
    // Throw an exception if deletion failed
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
