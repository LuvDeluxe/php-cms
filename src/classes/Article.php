<?php

class Article
{
  protected $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function get(int $id, bool $published = true)
  {
    $sql = "SELECT a.id, a.title, a.summary, a.content, a.created, a.category_id,
       a.member_id, a.published,
        c.name AS category,
        CONCAT(m.forename, ' ', m.surname) AS author,
        i.id AS image_id,
        i.file AS image_file,
        i.alt AS image_alt
        FROM article AS a
        JOIN category AS c ON a.category_id = c.id
        JOIN member AS m ON a.member_id = m.id
        LEFT JOIN image AS i ON a.image_id = i.id
        WHERE a.id = :id ";
    if ($published) {
      $sql .= "AND a.published = 1;";
    }
    return $this->db->runSQL($sql, [$id])->fetch();
  }

  public function getAll($published = true, $category = null, $member = null, $limit = 1000): array
  {
    $arguments['category'] = $category;
    $arguments['category1'] = $category;
    $arguments['member'] = $member;
    $arguments['member1'] = $member;
    $arguments['limit'] = $limit;
    $sql = "SELECT a.id, a.title, a.summary, a.category_id,
            a.member_id, a.published,
            c.name AS category,
            CONCAT(m.forename, ' ', m.surname) AS author,
            i.file AS image_file,
            i.alt AS image_alt
            FROM article AS a
            JOIN category AS c ON a.category_id = c.id
            JOIN member AS m ON a.member_id = m.id
            LEFT JOIN image AS i on a.image_id = i.id
            WHERE (a.category_id = :category OR :category1 IS null)
            AND (a.member_id = :member OR member1 IS null) ";

    if ($published) {
      $sql .= "AND a.published = 1 ";
    }
    $sql .= "ORDER BY a.id DESC LIMIT :limit";
    return $this->db->runSQL($sql, $arguments)->fetchAll();
  }

  public function searchCount(string $term): int
  {
    $arguments['term1'] = '%' . $term . '%';
    $arguments['term2'] = '%' . $term . '%';
    $arguments['term3'] = '%' . $term . '%';
    $sql = "SELECT COUNT(title)
            FROM article
            WHERE (title LIKE :term1
            OR summary LIKE :term2
            OR content LIKE :term3)
            AND published = 1;";
    return $this->db->runSQL($sql, $arguments)->fetchColumn();
  }

  public function search(string $term, int $show = 3, int $from = 0): array
  {
    $arguments['term1'] = '%' . $term . '%';
    $arguments['term2'] = '%' . $term . '%';
    $arguments['term3'] = '%' . $term . '%';
    $arguments['show'] = $show;
    $arguments['from'] = $from;
    $sql = "SELECT a.id, a.title, a.summary, a.created, a.category_id, a.member_id,
                     c.name      AS category,
                     CONCAT(m.forename, ' ', m.surname) AS author,
                     i.file      AS image_file, 
                     i.alt       AS image_alt

                FROM article     AS a
                JOIN category    AS c    ON a.category_id = c.id
                JOIN member      AS m    ON a.member_id   = m.id
                LEFT JOIN image  AS i    ON a.image_id    = i.id

               WHERE (a.title     LIKE :term1 
                  OR a.summary   LIKE :term2
                  OR a.content   LIKE :term3)
                 AND a.published = 1
               ORDER BY a.id DESC
               LIMIT :show 
              OFFSET :from;";

    return $this->db->runSQL($sql, $arguments)->fetchAll();
  }
}
