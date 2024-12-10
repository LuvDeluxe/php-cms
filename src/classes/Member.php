<?php
/**
 * Represents a Member in the system, handling member-related database operations.
 */
class Member
{
  /**
   * The database connection object used for querying member data.
   * @var Database
   */
  protected $db;

  /**
   * Constructor for Member class.
   *
   * @param Database $db The database connection object.
   */
  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  /**
   * Retrieves a specific member by their ID from the database.
   *
   * @param int $id The ID of the member to retrieve.
   * @return array|null Returns an associative array with member details or null if not found.
   */
  public function get(int $id)
  {
    $sql = "SELECT id, forename, surname, joined, picture
            FROM member
            WHERE id = :id;";
    return $this->db->runSQL($sql, [$id])->fetch();
  }

  /**
   * Retrieves all members from the database.
   *
   * @return array An array of associative arrays, each containing member details.
   */
  public function getAll(): array
  {
    $sql = "SELECT id, forename, surname, joined, picture
            FROM member;";
    return $this->db->runSQL($sql)->fetchAll();
  }
}