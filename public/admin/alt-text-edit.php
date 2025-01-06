<?php
declare(strict_types=1);
include '../../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$article = [];
$errors = ['alt' => '',
  'warning' => ''];

if (!$id) {
  redirect('admin/articles.php', ['failure' => 'Article not found']);
}
$article = $cms->getArticle()->get($id, false);
if (!$article['image_file']) {
  redirect('admin/article.php', ['id' => $id]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $article['image_alt'] = $_POST('image_alt');

  $errors['alt'] = (Validate::isText($article['image_alt'], 1, 254)) ? '' : 'Alt text for image should be 1 - 254 characters.';

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

