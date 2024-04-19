<?php
declare(strict_types = 1);
require 'includes/database-connection.php';
require 'includes/functions.php';

$term = filter_input(INPUT_GET, 'term'); // hold search term
$show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 3; // N of results to show per page
$from = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0; // gets N of results to skip
$count = 0;
$articles = [];

if ($term) {
  $arguments['term1'] = '%$term%';
  $arguments['term2'] = '%$term%';
  $arguments['term3'] = '%$term%';
}

$sql = "SELECT COUNT(title) FROM article
WHERE title LIKE :term1
  OR summary LIKE :term2
  OR content LIKE :term3
AND published = 1;";

$count = pdo($pdo, $sql, $arguments)->fetchColumn();

if ($count > 0) {
  $arguments['show'] = $show;
  $arguments['from'] = $from;

  $sql = "SELECT a.id, a.title, a.summary, a.category_id, a.member_id,
  c.name AS category,
  CONCAT(m.forename, ' ', m.surname) AS author,
  i.file AS image_file,
  i.alt AS image_alt
  FROM article AS a
  JOIN category AS c ON a.category_id = c.id
  JOIN member AS m ON a.member_id = m.id
  LEFT JOIN image AS i ON a.image_id = i.id
  WHERE a.title LIKE :term1
  OR a.summary LIKE :term2
  OR a.content LIKE :term3
  AND a.published = 1
  ORDER BY a.id DESC
  LIMIT :show
  OFFSET :from;";

  $articles = pdo($pdo, $sql, $arguments)->fetchAll();
}