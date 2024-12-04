<?php
/**
 * The Database class extends PHP's PDO (PHP Data Objects) to provide a simplified interface for database operations.
 *
 * This class:
 * - Enhances database connectivity with pre-defined options for security and usability.
 * - Implements a method to run SQL queries with or without prepared statements to prevent SQL injection.
 * - Automatically sets PDO to throw exceptions on errors, promoting robust error handling.
 * - Uses associative arrays by default for fetching results, which simplifies data manipulation in many scenarios.
 * - Provides an abstraction layer over PDO, potentially allowing for easier testing or mocking of database operations.
 */
class Database extends PDO
{
  /**
   * Constructor for the Database class, extending PDO.
   * Sets up default PDO options for better database interaction.
   *
   * @param string $dsn The Data Source Name containing the information required to connect to the database.
   * @param string $username The user name for the DSN string.
   * @param string $password The password for the DSN string.
   * @param array $options An associative array of PDO options.
   */
  public function __construct(string $dsn, string $username, string $password, array $options = [])
  {
    // Default settings for PDO connection:
    // Fetch mode set to associative array for easier data handling
    // Disable emulation of prepared statements for security and performance
    // Throw exceptions on error for better error handling
    $default_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
    $default_options[PDO::ATTR_EMULATE_PREPARES] = false;
    $default_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    // Merge default options with user-supplied options
    $options = array_replace($default_options, $options);
    parent::__construct($dsn, $username, $password, $options);
  }

  /**
   * Executes an SQL statement, either prepared or not.
   *
   * @param string $sql The SQL statement to run.
   * @param mixed $arguments If provided, these will be the parameters for prepared statements.
   * @return PDOStatement|false A PDOStatement object, or false if the query failed.
   */
  public function runSQL(string $sql, $arguments = null)
  {
    if (!$arguments) {
      // If no arguments, execute as a regular query
      return $this->query($sql);
    }
    // If arguments are provided, prepare and execute the statement
    $statement = $this->prepare($sql);
    $statement->execute($arguments);
    return $statement;
  }
}
