<?php
declare(strict_types=1);
http_response_code(404);
require_once 'src/bootstrap.php';

$navigation = $cms->getCategory()->getAll();
$section = '';
$title = 'Page not found';
$description = 'Page not found';
?>

<?php include APP_ROOT . '/includes/header.php'; ?>
<main class="container" id="content">
  <h1>Sorry! We cannot find this page.</h1>
  <p>Try the <a href="index.php">home page</a> or email us <a href="mailto:example@google.com">this email</a></p>
</main>
<?php include APP_ROOT . '/includes/footer.php'; ?>
<?php exit ?>
