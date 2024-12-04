<?php

/**
 * The Category class handles all category-related operations in the CMS, facilitating the management of content categories.
 *
 * This class provides functionality to:
 * - Retrieve individual categories or all categories.
 * - Count the total number of categories.
 * - Create, update, and delete categories with error handling for common database integrity issues.
 *
 * It ensures data consistency by checking for duplicate keys during creation or update, and handles foreign key constraints when deleting categories, providing a robust interface for category management within the CMS.
 */
class Category
{
  // Database connection object
  protected $db;

  /**
   * Constructor to initialize the database connection.
   *
   * @param Database $db An instance of the Database class for database operations.
   */
  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  /**
   * Retrieves a category by its ID.
   *
   * @param int $id The ID of the category.
   * @return array|null Returns an associative array with category details or null if not found.
   */
  public function get(int $id)
  {
    $sql = "SELECT id, name, description, navigation 
            FROM category WHERE id = :id;";
    return $this->db->runSQL($sql, [$id])->fetch();
  }

  /**
   * Fetches all categories with limited information.
   *
   * @return array An array of associative arrays containing category id, name, and navigation status.
   */
  public function getAll(): array
  {
    $sql = "SELECT id, name, navigation
            FROM category;";

    return $this->db->runSQL($sql)->fetchAll();
  }

  /**
   * Counts the total number of categories in the database.
   *
   * @return int The number of categories.
   */
  public function count(): int
  {
    $sql = "SELECT COUNT(id) FROM category;";
    return $this->db->runSQL($sql)->fetchColumn();
  }

  /**
   * Creates a new category in the database.
   *
   * @param array $category An associative array containing 'name', 'description', and 'navigation' keys.
   * @return bool True if the category was created successfully, false if there's a duplicate key error.
   * @throws PDOException For any other database errors except duplicate key.
   */
  public function create(array $category): bool
  {
    try {
      $sql = "INSERT INTO category (name, description, navigation)
                VALUES (:name, :description, :navigation);";
      $this->db->runSQL($sql, $category);
      return true;
    } catch (PDOException $e) {
      // 1062 is the error code for duplicate key
      if ($e->errorInfo[1] === 1062) {
        return false;
      } else {
        throw $e;
      }
    }
  }

  /**
   * Updates an existing category in the database.
   *
   * @param array $category An associative array containing 'id', 'name', 'description', and 'navigation' keys.
   * @return bool True if the category was updated successfully, false if there's a duplicate key error.
   * @throws PDOException For any other database errors except duplicate key.
   */
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

  /**
   * Deletes a category from the database by ID.
   *
   * @param int $id The ID of the category to delete.
   * @return bool True if the category was deleted successfully, false if there's a foreign key constraint violation.
   * @throws PDOException For any other database errors except foreign key constraint failure.
   */
  public function delete(int $id): bool
  {
    try {
      $sql = "DELETE FROM category WHERE id = :id;";
      $this->db->runSQL($sql, [$id]);
      return true;
    } catch (PDOException $e) {
      // 1451 is the error code for foreign key constraint violation
      if ($e->errorInfo[1] === 1451) {
        return false;
      } else {
        throw $e;
      }
    }
  }
}