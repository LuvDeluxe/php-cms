<?php
/**
 * This script handles both the creation and editing of categories in the CMS.
 * It includes form validation, database operations for INSERT and UPDATE,
 * and displays error or success messages based on the operation outcome.
 */
// Ensures strict typing for all functions
declare(strict_types=1);
// Include necessary files for database connection, utility functions, and validation
include '../../src/bootstrap.php';

$id          = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$temp        = $_FILES['image']['tmp_name'] ?? '';
$destination = '';
$saved       = null;

$article = [
  'id'          => $id,
  'title'       => '',
  'summary'     => '',
  'content'     => '',
  'member_id'   => 0,
  'category_id' => 0,
  'image_id'    => null,
  'published'   => false,
  'image_file'  => '',
  'image_alt'   => '',
];                                                       // Article data
$errors  = [
  'warning'     => '',
  'title'       => '',
  'summary'     => '',
  'content'     => '',
  'author'      => '',
  'category'    => '',
  'image_file'  => '',
  'image_alt'   => '',
];

if ($id) {
  $article = $cms->getArticle()->get($id, false);
  if (!$article) {
    redirect('admin/articles.php', ['failure' => 'Article not found']);
  }
}

$saved_image = $article['image_file'] ? true : false;
$authors    = $cms->getMember()->getAll();
$categories = $cms->getCategory()->getAll();

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $errors['image_file'] = ($temp === '' and $_FILES['image']['error'] === 1) ? 'File too big ' : '';

  // If image was uploaded, get image data and validate
  if ($temp and $_FILES['image']['error'] == 0) {
    $article['image_alt'] = $_POST['image_alt'];

    // Validate image file
    $errors['image_file'] = in_array(mime_content_type($temp), MEDIA_TYPES)
      ? '' : 'Wrong file type. ';
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $errors['image_file'] .= in_array($extension, FILE_EXTENSIONS)
      ? '' : 'Wrong file extension. ';
    $errors['image_file'] .= ($_FILES['image']['size'] <= MAX_SIZE)
      ? '' : 'File too big. ';
    $errors['image_alt']  = (Validate::isText($article['image_alt'], 1, 254))
      ? '' : 'Alt text must be 1-254 characters.';

    // If image file is valid, specify the location to save it
    if ($errors['image_file'] === '' and $errors['image_alt'] === '') {
      $article['image_file'] = create_filename($_FILES['image']['name'], UPLOADS);
      $destination = UPLOADS . $article['image_file'];
    }
  }

  // Get article data
  $article['title']       = $_POST['title'];
  $article['summary']     = $_POST['summary'];
  $article['content']     = $_POST['content'];
  $article['member_id']   = $_POST['member_id'];
  $article['category_id'] = $_POST['category_id'];
  $article['published']   = (isset($_POST['published']) and ($_POST['published'] == 1)) ? 1 : 0;

  // Validate article data and create error messages if it is invalid
  $errors['title']    = Validate::isText($article['title'], 1, 80)
    ? '' : 'Title must be 1-80 characters';
  $errors['summary']  = Validate::isText($article['summary'], 1, 254)
    ? '' : 'Summary must be 1-254 characters';
  $errors['content']  = Validate::isText($article['content'], 1, 100000)
    ? '' : 'Article must be 1-100,000 characters';
  $errors['member']   = Validate::isMemberId($article['member_id'], $authors)
    ? '' : 'Please select an author';
  $errors['category'] = Validate::isCategoryId($article['category_id'], $categories)
    ? '' : 'Please select a category';

  $invalid = implode($errors);

  // If data is valid, if so update database
  if ($invalid) {
    $errors['warning'] = 'Please correct form errors';
  } else {
    $arguments = $article;
    if ($id) {
      $saved = $cms->getArticle()->update($arguments, $temp, $destination);
    } else {
      unset($arguments['id']);
      $saved = $cms->getArticle()->create($arguments, $temp, $destination);
    }

    if ($saved == true) {
      redirect('admin/articles.php', ['success' => 'Article saved']);
    } else {
      $errors['warning'] = 'Article title already in use';
    }
  }
  $article['image_file'] = $saved_image ? $article['image_file'] : '';
}

?>

<?php include APP_ROOT . '/public/includes/admin-header.php' ?>
<main class="container admin" id="content">
    <form action="category.php?id=<?= $id ?>" method="post" class="narrow">
        <h1>Edit Category</h1>
      <?php if ($errors['warning']) { ?>
          <div class="alert alert-danger"><?= $errors['warning'] ?></div>
      <?php } ?>

        <div class="form-group">
            <label for="name">Name: </label>
            <input type="text" name="name" id="name"
                   value="<?= html_escape($category['name']) ?>" class="form-control">
            <span class="errors"><?= $errors['name'] ?></span>
        </div>

        <div class="form-group">
            <label for="description">Description: </label>
            <textarea name="description" id="description"
                      class="form-control"><?= html_escape($category['description']) ?></textarea>
            <span class="errors"><?= $errors['description'] ?></span>
        </div>

        <div class="form-check">
            <input type="checkbox" name="navigation" id="navigation"
                   value="1" class="form-check-input"
              <?= ($category['navigation'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="navigation">Navigation</label>
        </div>

        <input type="submit" value="Save" class="btn btn-primary btn-save">
    </form>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php' ?>

