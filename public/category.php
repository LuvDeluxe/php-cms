<?php
/**
 * Category Listing Page
 *
 * This script manages:
 * - Fetching a specific category by ID from the URL query string.
 * - Validation of the category ID.
 * - Loading category details and associated articles from the CMS.
 * - Displaying the category name, description, and a grid of article summaries within that category.
 * - Includes error handling for missing or invalid categories, redirecting to a 404 page if necessary.
 * - Utilizes strict typing for better type safety.
 */

// Enable strict typing for better type safety
declare(strict_types=1);

// Include necessary bootstrap file for initialization
include 'src/bootstrap.php';

// Retrieve and validate the category ID from GET parameters
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  // If no valid ID is provided, show a 404 page
  include APP_ROOT . '/page-not-found.php';
}
// Fetch the category details from the CMS using the validated ID
$category = $cms->getCategory()->get($id);
if (!$category) {
  // If no category is found for the given ID, show a 404 page
  include APP_ROOT . '/page-not-found.php';
}

// Retrieve articles associated with this category
$articles = $cms->getArticle()->getAll(true, $id);
// Gather navigation data for all categories
$navigation = $cms->getCategory()->getAll();
// Store category details for use in the template
$section = $category['id'];
$title = $category['name'];
$description = $category['description'];
?>

<?php include APP_ROOT . '/includes/header.php'; ?>
    <main class="container" id="content">
        <section class="header">
            <h1><?= html_escape($category['name']); ?></h1>
            <p><?= html_escape($category['description']); ?></p>
        </section>

        <section class="grid">
          <?php foreach ($articles as $article) { ?>
              <article class="summary">
                  <a href="article.php?id=<?= $article['id'] ?>">
                      <img src="uploads/<?= html_escape($article['image_file'] ?? 'blank.png') ?>"
                           alt="<?= html_escape($article['image_alt']) ?>">
                      <h2><?= html_escape($article['title']) ?></h2>
                      <p><?= html_escape($article['summary']) ?></p>
                  </a>
                  <p class="credit">
                      Posted in <a href="category.php?id=<?= $article['category_id'] ?>">
                      <?= html_escape($article['category']) ?></a>
                      by <a href="member.php?id=<?= $article['member_id'] ?>">
                      <?= html_escape($article['author']) ?></a>
                  </p>
              </article>
          <?php } ?>
        </section>
    </main>
<?php include APP_ROOT . '/includes/footer.php' ?>