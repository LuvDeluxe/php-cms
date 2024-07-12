<?php
declare(strict_types=1);
include '../includes/database-connection.php';
include '../includes/functions.php';
include '../includes/validate.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$image = [
  'file' => '',
  'alt' => ''
];

$errors = [
  'alt' => '',
  'warning' => ''
];

if ($id) {
  $sql = "SELECT i.id, i.file, i.alt
        FROM image AS i
        JOIN article AS a
        ON i.id = a.image_id
        WHERE a.id = :id;";
  $image = pdo($pdo, $sql, [$id])->fetch();
}

if (!$image) {
  redirect('article.php', ['id' => $id]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $image['alt'] = $_POST['image_alt'];

  $errors['alt'] = (is_text($image['alt'], 1, 254)) ? '' : 'Alt text for image should be 1 - 254 chars.';

  if ($errors['alt']) {
    $errors['warning'] = 'Please correct error below';
  } else {
    unset($image['file']);
    $sql = "UPDATE image
                SET alt = :alt
                WHERE id = :id;";
    pdo($pdo, $sql, $image);
    redirect('article.php', ['id' => $id]);
  }
}
?>

<?php include '../includes/admin-header.php'; ?>
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
<?php include '../includes/admin-footer.php'; ?>

