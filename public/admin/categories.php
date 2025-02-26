<?php
/**
 * This script manages the display of categories in the CMS. It lists all categories with options to add,
 * edit, or delete them. It also handles displaying success or failure messages from category operations.
 */
// Ensures all functions require type declarations
declare(strict_types=1);
// Include bootstrap file
include '../../src/bootstrap.php';

// Handle GET parameters for success or failure messages
$success = $_GET['success'] ?? null;
$failure = $_GET['failure'] ?? null;

// Get all categories
$categories = $cms->getCategory()->getAll();

?>
<?php include APP_ROOT . '/public/includes/admin-header.php'; ?>
<main class="container" id="content">
  <section class="header">
    <h1>Categories</h1>
    <?php if($success) { ?><div class="alert alert-success"><?= $success ?></div><?php } ?>
    <?php if($failure) { ?><div class="alert alert-danger"><?= $failure ?></div><?php } ?>
    <p><a href="../category.php" class="btn btn-primary">Add new category</a></p>
  </section>

  <table class="categories">
    <tr>
      <th>Name</th>
      <th class="edit">Edit</th>
      <th class="delete">Delete</th>
    </tr>
    <?php foreach($categories as $category) { ?>
      <tr>
        <td>
          <?= html_escape($category['name']) ?>
        </td>
        <td>
          <a href="category.php?id=<?= $category['id']?>" class="btn btn-primary">Edit</a>
        </td>
        <td>
          <a href="category-delete.php?id=<?= $category['id']?>" class="btn btn-danger">Delete</a>
        </td>
      </tr>
    <?php } ?>
  </table>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php'; ?>
