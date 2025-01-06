<?php
/**
 * Home Page / Index Page
 *
 * This script:
 * - Retrieves the latest 6 articles from the CMS.
 * - Fetches all categories for navigation.
 * - Sets up basic page metadata like title and description.
 * - Displays a grid of article summaries including:
 *   - Article thumbnail (with a fallback image if none exists)
 *   - Title, summary, category, and author of each article.
 * - Includes error prevention with strict typing and uses HTML escaping for security.
 */

// Enable strict typing to ensure better code integrity
declare(strict_types=1);
// Include necessary bootstrap file for initialization
include './src/bootstrap.php';
// Fetch the latest 6 articles, sorted by date
$articles = $cms->getArticle()->getAll(true, null, null, 6);
// Retrieve all categories for the navigation menu
$navigation = $cms->getCategory()->getAll();
// Set page section to empty since this is the home page
$section = '';
// Set the page title
$title = 'Creative Folk';
// Set the page description for SEO purposes
$description = 'A collective of creatives to hire';
?>

<?php include APP_ROOT . '/includes/header.php' ?>
    <main class="container grid" id="content">
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
    </main>
<?php include APP_ROOT . '/includes/footer.php' ?>