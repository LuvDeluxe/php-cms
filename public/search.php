<?php
/**
 * Search Results Page
 *
 * This script:
 * - Processes search terms from URL parameters.
 * - Sets default values for result pagination (number of items to show per page and starting offset).
 * - Searches for articles based on the search term using CMS methods.
 * - Handles pagination, calculating total pages and current page number if results exceed the shown amount.
 * - Fetches categories for navigation.
 * - Sets up page metadata including a dynamic title based on the search term and description for SEO.
 * - Displays search form, result count, article summaries in a grid layout, and pagination for navigating through results.
 * - Uses strict typing for better type safety.
 */

// Enable strict typing for better type integrity
declare(strict_types=1);
// Include necessary bootstrap file for initialization
include './src/bootstrap.php';
// Get and sanitize the search term from URL
$term = filter_input(INPUT_GET, 'term');
// Set the number of results to show per page, defaulting to 3 if not specified
$show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 3;
// Set the starting offset for pagination, defaulting to 0 if not specified
$from = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0;
$count = 0; // Initialize count for total matches
$articles = []; // Array to hold the search results

// If a search term is provided, perform the search
if ($term) {
  // Get the number of matches for the search term
  $count = $cms->getArticle()->searchCount($term);
  if ($count > 0) {
    // Retrieve the articles matching the search term, with pagination
    $articles = $cms->getArticle()->search($term, $show, $from);
  }
}
// Setup for pagination if there are more results than can be shown on one page
if ($count > $show) {
  $total_pages = ceil($count / $show); // Calculate total number of pages
  $current_page = ceil($from / $show) + 1; // Determine current page number
}
// Fetch all categories for the site navigation
$navigation = $cms->getCategory()->getAll();
$section = ''; // Set to empty as there's no specific section for this page
// Construct the page title based on the search term
$title = 'Search results for ' . html_escape($term);
// Create a meta description for SEO purposes
$description = $title . ' on Creative Folk';

?>

<?php include APP_ROOT . '/includes/header.php' ?>
    <main class="container" id="content">
        <section class="header">
            <form action="search.php" method="get" class="form-search">
                <label for="search"><span>Search for: </span></label>
                <input type="text" name="term" value="<?= html_escape($term) ?>"
                       id="search" placeholder="Enter search term"
                /><input type="submit" value="Search" class="btn btn-search"/>
            </form>
          <?php if ($term) { ?><p><b>Matches found:</b> <?= $count ?></p><?php } ?>
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

      <?php if ($count > $show) { ?>
          <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
              <ul>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li>
                        <a href="?term=<?= $term ?>&show=<?= $show ?>&from=<?= (($i - 1) * $show) ?>"
                           class="btn <?= ($i == $current_page) ? 'active" aria-current="true' : '' ?>">
                          <?= $i ?>
                        </a>
                    </li>
                <?php } ?>
              </ul>
          </nav>
      <?php } ?>

    </main>
<?php include APP_ROOT . '/includes/footer.php' ?>