<?php

/**
 * Exception
 *
 * @project RusranUtils
 *
 * @author  Yuri Ashurkov (rusranx)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace RusranUtils\Exception;

class MethodNotFoundException extends Exception
{
	public function __construct($class, $name)
	{
		$message = sprintf("Call to undefined method %s::%s()", $class, $name);
//		parent::__construct($message);
		trigger_error($message, E_USER_ERROR);
	}
}