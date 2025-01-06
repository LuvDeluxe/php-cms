<?php
/**
 * Article Display Page
 *
 * This script handles:
 * - Retrieval of an article based on an ID from the URL query string.
 * - Validation of the article ID.
 * - Loading the article details from the CMS.
 * - Displaying the article with its image, title, content, author, and category information.
 * - Includes error handling for missing or invalid articles, redirecting to a 404 page if necessary.
 * - Uses strict typing for improved code reliability.
 */

// Enable strict typing to catch type-related errors early
declare(strict_types=1);
// Load initial bootstrap settings and configurations
include 'src/bootstrap.php';

// Retrieve and validate the article ID from GET parameters
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  // If no valid ID is provided, show a 404 page
  include APP_ROOT . '/page-not-found.php';
}

// Fetch the article from the CMS using the validated ID
$article = $cms->getArticle()->get($id);
if (!$article) {
  // If no article is found for the given ID, show a 404 page
  include 'page-not-found.php';
}

// Gather navigation data for categories
$navigation = $cms->getCategory()->getAll();
// Store the category ID of the article
$section = $article['category_id'];
// Store the article title
$title = $article['title'];
// Store the article summary for SEO or meta tags
$description = $article['summary'];
?>

<?php include APP_ROOT . '/includes/header.php'; ?>
<main class="article container">
    <section class="image">
        <img src="uploads/<?= html_escape($article['image_file'] ?? 'blank.png'); ?>" src="">
    </section>

    <section class="text">
        <h1><?= html_escape($article['title']) ?></h1>
        <div class="date"><?= format_date($article['created']); ?></div>
        <div class="content"><?= html_escape($article['content']); ?></div>
        <p class="credit">
            Posted in <a href="category.php?id=<?= $article['category_id'] ?>">
            <?= html_escape($article['category']); ?>
            </a>
            by <a href="member.php?id=<?= $article['member_id']; ?>">
            <?= html_escape($article['author']); ?>
            </a>
        </p>
    </section>
</main>
<?php include APP_ROOT . '/includes/footer.php'; ?>
