<?php
declare(strict_types=1);
include './src/bootstrap.php';

$term = filter_input(INPUT_GET, 'term');
$show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 3;
$from = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0;
$count = 0;
$articles = [];

if ($term) {
  $count = $cms->getArticle()->searchCount($term);
  if ($count > 0) {
    $articles = $cms->getArticle()->search($term, $show, $from);
  }
}

if ($count > $show) {
  $total_pages = ceil($count / $show);
  $current_page = ceil($from / $show) + 1;
}
$navigation = $cms->getCategory()->getAll();
$section = '';
$title = 'Search results for ' . html_escape($term);
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