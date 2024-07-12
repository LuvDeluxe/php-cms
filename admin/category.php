<?php

declare(strict_types=1);
include '../includes/database-connection.php';
include '../includes/functions.php';
include '../includes/validate.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = [
  'id' => $id,
  'name' => '',
  'description' => '',
  'navigation' => false
];
$errors = [
  'warning' => '',
  'name' => '',
  'description' => '',
];

if ($id) {
  $sql = "SELECT id, name, description, navigation
          FROM category
          WHERE id = :id;";
  $category = pdo($pdo, $sql, [$id])->fetch();
  if (!$category) {
    redirect('categories.php', ['failure' => 'Category not found']);
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $category['name'] = $_POST['name'];
  $category['description'] = $_POST['description'];
  $category['navigation'] = (isset($_POST['navigation']) and ($_POST['navigation'] === 1)) ? 1 : 0;

  $errors['name'] = (is_text($category['name'], 1, 24))
    ? '' : 'Name should be 1-24 characters.';
  $errors['description'] = (is_text($category['description'], 1, 254))
    ? '' : 'Description should be 1-254 characters.';
  $invalid = implode($errors);

  if ($invalid) {
    $errors['warning'] = 'Please correct errors';
  } else {
    $arguments = $category;
    if ($id) {
      $sql = "UPDATE category
              SET name = :name, description = :description,
                  navigation = :navigation
                  WHERE id = :id;";
    } else {
      unset($arguments['id']);
      $sql = "INSERT INTO category (name, description, navigation)
                VALUES(:name, :description, :navigation);";
    }
    try {
      pdo($pdo, $sql, $arguments);
      redirect('categories.php', ['success' => 'Category saved']);
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1062) {
        $errors['warning'] = 'Category name already in use';
      } else {
        throw $e;
      }
    }
  }
}
?>

<?php include '../includes/admin-header.php'; ?>
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
<?php include '../includes/admin-footer.php'; ?>

