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
}
