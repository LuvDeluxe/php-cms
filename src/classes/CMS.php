<?php
/**
 * The CMS class serves as the main entry point for the Content Management System operations.
 *
 * This class:
 * - Manages database connections and provides access to various CMS functionalities through different service classes.
 * - Utilizes lazy loading to instantiate service objects only when they are requested, which helps in optimizing performance by reducing unnecessary object creation.
 * - Ensures that all CMS functionalities have access to a database connection, centralizing database management.
 * - Provides methods to get instances of Article, Category, and Member management, which can be extended for other CMS features.
 *
 * The class adheres to the singleton pattern for its services, ensuring that only one instance of each service (like Article, Category, Member) is created per CMS instance, which promotes resource efficiency.
 */
class CMS
{
  /**
   * @var Database|null The database connection object.
   */
  protected $db = null;
  /**
   * @var Article|null The article management object.
   */
  protected $article = null;
  /**
   * @var Category|null The category management object.
   */
  protected $category = null;
  /**
   * @var Member|null The member management object.
   */
  protected $member = null;

  /**
   * Initializes the CMS with database credentials.
   *
   * @param string $dsn The Data Source Name for the database connection.
   * @param string $username The database username.
   * @param string $password The database password.
   */
  public function __construct($dsn, $username, $password)
  {
    // Create a new Database instance with the provided credentials
    $this->db = new Database($dsn, $username, $password);
  }

  /**
   * Lazy loads and returns the Article handler.
   *
   * @return Article The article management object.
   */
  public function getArticle()
  {
    if ($this->article === null) {
      // Instantiate Article only when first called to optimize resource usage
      $this->article = new Article($this->db);
    }
    return $this->article;
  }

  /**
   * Lazy loads and returns the Category handler.
   *
   * @return Category The category management object.
   */
  public function getCategory()
  {
    if ($this->category === null) {
      // Instantiate Category only when first called to optimize resource usage
      $this->category = new Category($this->db);
    }
    return $this->category;
  }

  /**
   * Lazy loads and returns the Member handler.
   *
   * @return Member The member management object.
   */
  public function getMember()
  {
    if ($this->member === null) {
      // Instantiate Member only when first called to optimize resource usage
      $this->member = new Member($this->db);
    }
    return $this->member;
  }
}