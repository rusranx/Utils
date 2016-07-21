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
	/**
	 * Returns random character
	 *
	 * @param int $type
	 * @return int|string
	 */
	public static function genSymbol($type = 0)
	{
		switch ($type) {
			case 0: //lower_letter
				return chr(mt_rand(97, 122));
				break;
			case 1: // upper_letter
				return chr(mt_rand(65, 90));
				break;
			case 2: // digit
				return mt_rand(0, 9);
				break;
			default:
				return 0;
		}
	}

	/**
	 * Returns random string
	 *
	 * @param int $length
	 * @return string
	 */
	public static function genPassword($length = 8)
	{
		$password = "";

		for ($i = 0; $i < $length; $i++) {
			$password .= self::genSymbol(mt_rand(0, 2));
		}

		return $password;
	}
}