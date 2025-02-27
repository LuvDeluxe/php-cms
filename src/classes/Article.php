<?php
/**
 * The Article class manages operations related to articles in the CMS, including retrieval, listing, and searching.
 *
 * This class provides methods to:
 * - Fetch a single article by its ID with optional filtering for published status.
 * - Retrieve multiple articles with various filtering options like publication status, category, and author.
 * - Count articles matching a search term.
 * - Search for articles with pagination support, allowing for keyword search across title, summary, and content.
 *
 * It uses dependency injection for the database connection, which is passed to the constructor, ensuring all database operations are performed through a consistent interface.
 */
class Article
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
   * Retrieves an article by its ID.
   *
   * @param int $id The ID of the article.
   * @param bool $published Whether to fetch only published articles, defaults to true.
   * @return array|null Returns an associative array with article details or null if not found.
   */
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

  /**
   * Fetches multiple articles based on various criteria.
   *
   * @param bool $published Whether to filter for published articles only.
   * @param int|null $category Category ID to filter by, or null for all categories.
   * @param int|null $member Member ID to filter by, or null for all members.
   * @param int $limit Maximum number of articles to return, defaults to 1000.
   * @return array An array of article objects.
   */
  public function getAll($published = true, $category = null, $member = null, $limit = 1000): array
  {
    // Prepare arguments for the SQL query
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

  /**
   * Counts the number of articles that match a search term.
   *
   * @param string $term The search term.
   * @return int The count of matching articles.
   */
  public function searchCount(string $term): int
  {
    // Prepare the search term for SQL LIKE clauses
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

  /**
   * Searches for articles by term with pagination.
   *
   * @param string $term The search term.
   * @param int $show Number of results to return per page, defaults to 3.
   * @param int $from Offset for pagination, defaults to 0.
   * @return array An array of articles matching the search term.
   */
  public function search(string $term, int $show = 3, int $from = 0): array
  {
    // Prepare the search term for SQL LIKE clauses
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

  // ADMIN METHODS
  public function count(): int
  {
    $sql = "SELECT COUNT(id) FROM article;";
    return $this->db->runSql($sql)->fetchColumn();
  }

  public function create(array $article, string $temporary, string $destination): bool
  {
    try {
      $this->db->beginTransaction();
      if ($destination) {
        // Crop and save file
        $imagick = new \Imagick($temporary);
        $imagick->cropThumbnailImage(1200, 700);
        $imagick->writeImage($destination);

        $sql = "INSERT INTO image (file, alt)
                        VALUES (:file, :alt);";
        $this->db->runSql($sql, [$article['image_file'], $article['image_alt']]);
        $article['image_id'] = $this->db->lastInsertId();
      }
      unset($article['image_file'], $article['image_alt']);
      $sql = "INSERT INTO article (title, summary, content, category_id, member_id,
                           image_id, published)
                    VALUES (:title, :summary, :content, :category_id, :member_id,
                           :image_id, :published);";
      $this->db->runSql($sql, $article);
      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollBack();
      if (file_exists($destination)) {
        unlink($destination);
      }
      if (($e instanceof PDOException) and ($e->errorInfo[1] === 1062)) {
        return false;
      } else {
        throw $e;
      }
    }
  }

  public function update(array $article, string $temporary, string $destination): bool
  {
    try {
      $this->db->beginTransaction();
      if ($destination) {

        $imagick = new \Imagick($temporary);
        $imagick->cropThumbnailImage(1200, 700);
        $imagick->writeImage($destination);

        $sql = "INSERT INTO image (file, alt)
                  VALUES (:file, :alt);";
        $this->db->runSql($sql, [$article['image_file'], $article['image_alt']]);
        $article['image_id'] = $this->db->lastInsertId();
      }
      // Remove unwanted elements from $article
      unset($article['category'], $article['created'], $article['author'], $article['image_file'], $article['image_alt']);
      $sql = "UPDATE article SET title = :title, summary = :summary, content = :content,
                           category_id = :category_id, member_id = :member_id,
                           image_id = :image_id, published = :published
                     WHERE id = :id;";
      $this->db->runSql($sql, $article)->rowCount();
      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollBack();
      if (file_exists($destination)) {
        unlink($destination);
      }
      if (($e instanceof PDOException) and ($e->errorInfo[1] === 1062)) {
        return false;
      } else {
        throw $e;
      }
    }
  }

  public function delete(int $id): bool
  {
    $sql = "DELETE FROM article WHERE id = :id;";
    $this->db->runSql($sql, [$id]);
    return true;
  }

  public function imageDelete(int $image_id, string $path, int $article_id)
  {
    $sql = "UPDATE article SET image_id = null
               WHERE id = :article_id;";
    $this->db->runSql($sql, [$article_id]);
    $sql = "DELETE FROM image
               WHERE id = :id;";
    $this->db->runSql($sql, [$image_id]);

    if (file_exists($path)) {
      unlink($path);
    }
  }

  public function altUpdate(int $image_id, string $alt)
  {
    $sql = "UPDATE image SET alt = :alt
               WHERE id = :article_id;";
    $this->db->runSql($sql, [$alt, $image_id]);
  }
}
