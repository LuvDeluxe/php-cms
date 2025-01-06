<?php
declare(strict_types=1);
include '../../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$temp = $_FILES['image']['tmp_name'] ?? '';
$destination = '';
$saved = null;

$article = [
  'id' => $id,
  'title' => '',
  'summary' => '',
  'content' => '',
  'member_id' => 0,
  'category_id' => 0,
  'image_id' => null,
  'published' => false,
  'image_file' => '',
  'image_alt' => '',
];

$errors = [
  'warning' => '',
  'title' => '',
  'summary' => '',
  'content' => '',
  'author' => '',
  'category' => '',
  'image_file' => '',
  'image_alt' => '',
];

if ($id) {
  $article = $cms->getArticle()->get($id, false);
  if (!$article) {
    redirect('admin/articles.php', ['failure' => 'Article not found']);
  }
}

$saved_image = $article['image_file'] ? true : false;
$authors = $cms->getMember()->getAll();
$categories = $cms->getCategory()->getAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $errors['image_file'] = ($temp === '' and $_FILES['image']['error'] === 1) ? 'File too big' : '';

  if ($temp and $_FILES['image']['error'] == 0) {
    $article['image_alt'] = $_POST['image_alt'];

    $errors['image_file'] = in_array(mime_content_type($temp), MEDIA_TYPES) ? '' : 'Wrong file type';
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $errors['image_file'] .= in_array($extension, FILE_EXTENSIONS) ? '' : 'Wrong file extension';
    $errors['image_file'] .= ($_FILES['image']['size'] <= MAX_SIZE) ? '' : 'File too big';
    $errors['image_alt'] = 

  }
}
?>

<?php include '../includes/admin-header.php'; ?>
<form action="article.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
    <main class="container admin" id="content">

        <h1>Edit Article</h1>
      <?php if ($errors['warning']) { ?>
          <div class="alert alert-danger"><?= $errors['warning'] ?></div>
      <?php } ?>

        <div class="admin-article">
            <section class="image">
              <?php if (!$article['image_file']) { ?>
                  <label for="image">Upload image:</label>
                  <div class="form-group image-placeholder">
                      <input type="file" name="image" class="form-control-file" id="image"><br>
                      <span class="errors"><?= $errors['image_file'] ?></span>
                  </div>
                  <div class="form-group">
                      <label for="image_alt">Alt text: </label>
                      <input type="text" name="image_alt" id="image_alt" value="" class="form-control">
                      <span class="errors"><?= $errors['image_alt'] ?></span>
                  </div>
              <?php } else { ?>
                  <label>Image:</label>
                  <img src="../uploads/<?= html_escape($article['image_file']) ?>"
                       alt="<?= html_escape($article['image_alt']) ?>">
                  <p class="alt"><strong>Alt text:</strong> <?= html_escape($article['image_alt']) ?></p>
                  <a href="alt-text-edit.php?id=<?= $article['id'] ?>" class="btn btn-secondary">Edit alt text</a>
                  <a href="image-delete.php?id=<?= $id ?>" class="btn btn-secondary">Delete image</a><br><br>
              <?php } ?>
            </section>

            <section class="text">
                <div class="form-group">
                    <label for="title">Title: </label>
                    <input type="text" name="title" id="title" value="<?= html_escape($article['title']) ?>"
                           class="form-control">
                    <span class="errors"><?= $errors['title'] ?></span>
                </div>
                <div class="form-group">
                    <label for="summary">Summary: </label>
                    <textarea name="summary" id="summary"
                              class="form-control"><?= html_escape($article['summary']) ?></textarea>
                    <span class="errors"><?= $errors['summary'] ?></span>
                </div>
                <div class="form-group">
                    <label for="content">Content: </label>
                    <textarea name="content" id="content"
                              class="form-control"><?= html_escape($article['content']) ?></textarea>
                    <span class="errors"><?= $errors['content'] ?></span>
                </div>
                <div class="form-group">
                    <label for="member_id">Author: </label>
                    <select name="member_id" id="member_id">
                      <?php foreach ($authors as $author) { ?>
                          <option value="<?= $author['id'] ?>"
                            <?= ($article['member_id'] == $author['id']) ? 'selected' : ''; ?>>
                            <?= html_escape($author['forename'] . ' ' . $author['surname']) ?></option>
                      <?php } ?>
                    </select>
                    <span class="errors"><?= $errors['author'] ?></span>
                </div>
                <div class="form-group">
                    <label for="category">Category: </label>
                    <select name="category_id" id="category">
                      <?php foreach ($categories as $category) { ?>
                          <option value="<?= $category['id'] ?>"
                            <?= ($article['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?= html_escape($category['name']) ?></option>
                      <?php } ?>
                    </select>
                    <span class="errors"><?= $errors['category'] ?></span>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="published" value="1" class="form-check-input" id="published"
                      <?= ($article['published'] == 1) ? 'checked' : ''; ?>>
                    <label for="published" class="form-check-label">Published</label>
                </div>
                <input type="submit" name="update" value="Save" class="btn btn-primary">
            </section>
        </div>
    </main>
</form>
<?php include '../includes/admin-footer.php'; ?>
