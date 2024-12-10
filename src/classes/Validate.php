<?php

/**
 * Provides static methods for various validation checks.
 */
class Validate
  /**
   * Checks if a number is within a specified range.
   *
   * @param mixed $number The number to check.
   * @param int $min The minimum value allowed (inclusive).
   * @param int $max The maximum value allowed (inclusive).
   * @return bool True if the number is within the range, false otherwise.
   */
{
  public static function isNumber($number, $min = 0, $max = 100): bool
  {
    return ($number >= $min and $number <= $max);
  }

  /**
   * Validates if the length of a string falls within the given range.
   *
   * @param string $string The string to validate.
   * @param int $min The minimum length allowed (inclusive).
   * @param int $max The maximum length allowed (inclusive).
   * @return bool True if the string length is within the specified range, false otherwise.
   */
  public static function isText(string $string, int $min = 0, int $max = 1000): bool
  {
    $length = mb_strlen($string);
    return ($length >= $min and $length <= $max);
  }

  /**
   * Checks if a member ID exists within a given list of members.
   *
   * @param mixed $member_id The ID to check for existence.
   * @param array $member_list An array of member objects or arrays where each element has an 'id' key.
   * @return bool True if the member ID is found, false otherwise.
   */
  public static function isMemberId($member_id, array $member_list): bool
  {
    foreach ($member_list as $member) {
      if ($member['id'] == $member_id) {
        return true;
      }
    }
    return false;
  }

  /**
   * Checks if a category ID exists within a given list of categories.
   *
   * @param mixed $category_id The ID to check for existence.
   * @param array $category_list An array of category objects or arrays where each element has an 'id' key.
   * @return bool True if the category ID is found, false otherwise.
   */
  public static function isCategoryId($category_id, array $category_list): bool
  {
    foreach ($category_list as $category) {
      if ($category['id'] == $category_id) {
        return true;
      }
    }
    return false;
  }
}