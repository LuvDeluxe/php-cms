<?php

class Category
{
  protected $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function get(int $id)
  {
    $sql = "SELECT id, name, description, navigation 
            FROM category WHERE id = :id;";
    return $this->db->runSQL($sql, [$id])->fetch();
  }

  public function getAll(): array
  {
    $sql = "SELECT id, name, navigation
            FROM category;";

    return $this->db->runSQL($sql)->fetchAll();
  }

  public function count(): int
  {
    $sql = "SELECT COUNT(id) FROM category;";
    return $this->db->runSQL($sql)->fetchColumn();
  }

  public function create(array $category): bool
  {
    try {
      $sql = "INSERT INTO category (name, description, navigation)
                VALUES (:name, :description, :navigation);";
      $this->db->runSQL($sql, $category);
      return true;
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1062) {
        return false;
      } else {
        throw $e;
      }
    }
  }

  public function update(array $category): bool
  {
    try {
      $sql = "UPDATE category
              SET name = :name, description = :description, navigation = :navigation
              WHERE id = :id;";
      $this->db->runSQL($sql, $category);
      return true;
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1062) {
        return false;
      } else {
        throw $e;
      }
    }
  }

  public function delete(int $id): bool
  {
    try {
      $sql = "DELETE FROM category WHERE id = :id;";
      $this->db->runSQL($sql, [$id]);
      return true;
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1451) {
        return false;
      } else {
        throw $e;
      }
    }
  }
}