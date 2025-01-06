<?php
/**
 * Member Profile Page
 *
 * This script:
 * - Validates and retrieves a member's ID from the URL.
 * - Fetches member details and their articles from the CMS.
 * - Sets up page metadata including title and description.
 * - Displays member information and a grid of their published articles.
 * - Includes error handling for missing or invalid member IDs, redirecting to a 404 page.
 * - Uses strict typing for better type safety.
 */

// Enable strict typing for better type integrity
declare(strict_types=1);
// Include necessary bootstrap file for initialization
include './src/bootstrap.php';
// Get and validate the member ID from the URL query string
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  // If no valid ID, redirect to 404 page
  include APP_ROOT . '/page-not-found.php';
}

// Fetch member details from CMS using the validated ID
$member = $cms->getMember()->get($id);
// If no member found, redirect to 404 page
if (!$member) {
  include APP_ROOT . '/page-not-found.php';
}
// Retrieve all articles by this member, sorted by date (assuming true means latest first)
$articles = $cms->getArticle()->getAll(true, null, $id);
// Fetch all categories for the navigation menu
$navigation = $cms->getCategory()->getAll();
// Set the section identifier (empty if there's no specific section for this page)
$section = '';
// Construct the title using the member's full name
$title = $member['forename'] . ' ' . $member['surname'];
// Create a meta description for SEO, combining the member's name and site identifier
$description = $title . ' on Creative Folk';
?>

<?php include APP_ROOT . '/includes/header.php' ?>
<main class="container" id="content">
    <section class="header">
        <h1><?= html_escape($member['forename'] . ' ' . $member['surname']); ?></h1>
        <p class="member"><b>Member since:</b> <?= format_date($member['joined']); ?></p>
        <img src="uploads/<?= html_escape($member['picture'] ?? 'blank.png'); ?>"
             alt="<?= html_escape($member['forename']) ?>" class="profile"><br>
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
?>
<?php include APP_ROOT . '/includes/footer.php' ?>
