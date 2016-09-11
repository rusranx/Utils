<?php
/**
 * MeasureWord
 *
 * @project RusranUtils
 *
 * @author  Yuri Ashurkov (rusranx)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace RusranUtils;

use RusranUtils\Exception\InvalidArgumentException;

class MeasureWord
{
	const CASE_FORMS = [2, 0, 1, 1, 1, 2];

	private $forms = [];

	/**
	 * MeasureWord constructor.
	 *
	 * @param array|string[] $oneFormOrForms
	 * @param null|string    $threeForm
	 * @param null|string    $fiveForm
	 * @throws InvalidArgumentException
	 */
	public function __construct($oneFormOrForms, $threeForm = null, $fiveForm = null)
	{
		switch (true) {
			case is_array($oneFormOrForms):
				$this->forms = $oneFormOrForms;
				break;
			case (is_string($oneFormOrForms) && is_string($threeForm) && is_string($fiveForm)):
				$this->forms = [$oneFormOrForms, $threeForm, $fiveForm];
				break;
			default:
				throw new InvalidArgumentException("Invalid arguments");
		}
	}

	/**
	 * Return measure word for value
	 *
	 * @param integer $value
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function getForm($value = 1)
	{
		if (!is_integer($value))
			throw new InvalidArgumentException("Argument must be an integer");

		return $this->forms[ ($value % 100 > 10 && $value < 20) ? 2 : self::CASE_FORMS[ min($value % 10, 5) ] ];
	}

	/**
	 * Return measure word for value
	 *
	 * @param $forms
	 * @param $value
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public static function getFormByForms($forms, $value)
	{
		$mw = new MeasureWord($forms);

		return $mw->getForm($value);
	}
}