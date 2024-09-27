<?php

class Member
{
  protected $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function get(int $id)
  {
    $sql = "SELECT id, forename, surname, joined, picture
            FROM member
            WHERE id = :id;";
    return $this->db->runSQL($sql, [$id])->fetch();
  }

  public function getAll(): array
  {
    $sql = "SELECT id, forename, surname, joined, picture
            FROM member;";
    return $this->db->runSQL($sql)->fetchAll();
  }
}