<?php
/**
 * Generator
 *
 * @project RusranUtils
 *
 * @author  Yuri Ashurkov (rusranx)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace RusranUtils;


class Generator
{
	const SYMBOL_LOWER = 0;
	const SYMBOL_UPPER = 1;
	const SYMBOL_DIGIT = 2;

	/**
	 * Returns random character
	 *
	 * @param int $type
	 * @return int|string
	 */
	public static function getSymbol($type = self::SYMBOL_LOWER)
	{
		switch ($type) {
			case self::SYMBOL_LOWER:
				return chr(mt_rand(97, 122));
				break;
			case self::SYMBOL_UPPER:
				return chr(mt_rand(65, 90));
				break;
			case self::SYMBOL_DIGIT:
				return mt_rand(0, 9);
				break;
			default:
				return null;
		}
	}

	/**
	 * Returns random string
	 *
	 * @param int $length
	 * @return string
	 */
	public static function getPassword($length = 8)
	{
		$password = "";

		for ($i = 0; $i < $length; $i++) {
			$password .= self::getSymbol(mt_rand(0, 2));
		}

		return $password;
	}
}