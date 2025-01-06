<?php
/**
 * Alt Text Edit Page
 *
 * This script allows admin users to edit the alt text for images associated with articles.
 * - It validates the article ID from the URL.
 * - Fetches article details including image information.
 * - Handles POST requests to update the alt text.
 * - Displays a form for editing and an error message if the input is invalid.
 * - Uses strict typing for better type safety.
 */

// Enable strict typing for better type integrity
declare(strict_types=1);
// Include the CMS bootstrap file
include '../../src/bootstrap.php';
// Get and validate the article ID from the URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$article = []; // Initialize article array
$errors = ['alt' => '', // Initialize error messages array

  'warning' => ''];
// Redirect if no valid ID provided
if (!$id) {
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}
// Fetch article details from the CMS
$article = $cms->getArticle()->get($id, false);
// Redirect if no image is associated with the article
if (!$article['image_file']) {
  redirect('admin/article.php', ['id' => $id]);
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Retrieve submitted alt text
  $article['image_alt'] = $_POST('image_alt');
  // Validate the alt text length
  $errors['alt'] = (Validate::isText($article['image_alt'], 1, 254)) ? '' : 'Alt text for image should be 1 - 254 characters.';
  // If there are no errors, update the alt text in the database
  if ($errors['alt']) {
    $errors['warning'] = 'Please correct error below';
  } else {
      // TODO: Define altUpdate
    $cms->getArticle()->altUpdate($article['image_id'], $article['image_alt']);
    redirect('admin/article.php', ['id' => $id]);
  }
}
?>

<?php include APP_ROOT . '/public/includes/admin-header.php'; ?>
<main class="container admin" id="content">
    <form action="alt-text-edit.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Update alt text</h1>
      <?php if ($errors['warning']) { ?>
          <div class="alert alert-danger"><?= $errors['warning'] ?></div> <?php } ?>

        <div class="form-group">
            <label for="image_alt">Alt text: </label>
            <input type="text" name="image_alt" id="image_alt" value="<?= html_escape($image['alt']) ?>"
                   class="form-control">
            <span class="errors"><?= $errors['alt'] ?></span>
        </div>

        <div class="form-group">
            <input type="submit" name="delete" value="Confirm" class="btn btn-primary btn-save">
        </div>

        <img src="../uploads/<?= $image['file'] ?>" alt="<?= html_escape($image['alt']) ?>">
    </form>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php' ?>

