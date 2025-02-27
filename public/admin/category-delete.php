<?php
/**
 * This script handles the deletion of a category from the CMS. It first checks if the category exists,
 * then provides a confirmation form for the user to delete it. It also manages error handling for
 * situations where the category cannot be deleted (like having associated articles).
 */
// Ensures strict typing for all functions
declare(strict_types = 1);

// Include bootstrap
include '../../src/bootstrap.php';

// Retrieve and validate the category ID from GET parameters
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = '';
$deleted = null;

// Redirect if no valid ID is provided
if (!$id) {
  redirect('admin/categories.php', ['failure' => 'Category not found']);
}

$category = $cms->getCategory()->get($id);
// Redirect if category does not exist
if (!$category) {
  redirect('admin/categories.php', ['failure' => 'Category not found']);
}

// Handle form submission for category deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if ($id) {
    $deleted  = $cms->getCategory()->delete($id);
    if ($deleted  === true) {
      redirect('admin/categories.php', ['success' => 'Category deleted']);
    }
    if ($deleted  === false) {
      redirect('admin/categories.php', ['failure' => 'Category contains articles that 
            must be moved or deleted before you can delete the category']);
    }
  }
}

?>
<?php include APP_ROOT . '/public/includes/admin-header.php'; ?>

<main class="container admin" id="content">
    <main class="container admin" id="content">
        <form action="category-delete.php?id=<?= $id ?>" method="POST" class="narrow">
            <h1>Delete Category</h1>
            <p>Click confirm to delete the category: <em><?= html_escape($category['name']) ?></em></p>
            <input type="submit" name="delete" value="Confirm" class="btn btn-primary">
            <a href="categories.php" class="btn btn-danger">Cancel</a>
        </form>
    </main>
<?php include APP_ROOT . '/public/includes/admin-footer.php'; ?>