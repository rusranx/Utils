<?php
/*
 * Dumphper
 * @project RusranUtils
 * 
 * @author Yuri Ashurkov (rusranx)
 */

namespace RusranUtils;


class TimeConverter
{

	private
		$_unixtime = 0,
		$_seconds = 0,
		$_minutes = 0,
		$_hours = 0,
		$_days = 0,
		$_months = 0,
		$_years = 0;
	private
		$_forms = [
		[
			["год", "года", "лет"],
			["месяц", "месяца", "месяцев"],
			["день", "дня", "дней"],
			["час", "часа", "часов"],
			["минута", "минуты", "минут"],
			["секунда", "секунды", "секунд"]
		],
		[
			["г", "г", "л"],
			["мес", "мес", "мес"],
			["д", "д", "д"],
			["ч", "ч", "ч"],
			["мин", "мин", "мин"],
			["сек", "сек", "сек"]
		]
	];

	public function __construct($unixtime)
	{
		$this->_unixtime = $unixtime;
		$this->_convertTime();
	}

	private function _convertTime()
	{
		$time = $this->_unixtime;
		$this->_seconds = $time % 60;
		$time -= $this->_seconds;
		$this->_minutes = ($time - ($time % 60)) / 60 % 60;
		$time -= $this->_minutes * 60;
		$this->_hours = ($time - ($time % 3600)) / 3600 % 24;
		$time -= $this->_hours * 3600;
		$this->_days = ($time - ($time % 86400)) / 86400 % 30;
		$time -= $this->_days * 86400;
		$this->_months = ($time - ($time % 2592000)) / 2592000 % 12;
		$time -= $this->_months * 2592000;
		$this->_years = ($time - ($time % 31104000)) / 31104000;
	}

	public function getUnixtime()
	{
		return $this->_unixtime;
	}

	public function getHuman($max_groups = INF, $short = false)
	{
		$short = (bool)$short;
		
		if ($max_groups < 1) $max_groups = INF;
		$for_print = [];

		if ($this->_years > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_years,
				MeasureWord::getFormByForms($this->_forms[ $short ][0], intval($this->_years))
			); // years
		}

		if ($this->_months > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_months,
				MeasureWord::getFormByForms($this->_forms[ $short ][1], intval($this->_months))
			); // months
		}

		if ($this->_days > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_days,
				MeasureWord::getFormByForms($this->_forms[ $short ][2], intval($this->_days))
			); // days
		}

		if ($this->_hours > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_hours,
				MeasureWord::getFormByForms($this->_forms[ $short ][3], intval($this->_hours))
			); // hours
		}

		if ($this->_minutes > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_minutes,
				MeasureWord::getFormByForms($this->_forms[ $short ][4], intval($this->_minutes))
			); // minutes
		}

		if ($this->_seconds > 0 || empty($for_print)) {
			$for_print[] = sprintf("%d %s",
				$this->_seconds,
				MeasureWord::getFormByForms($this->_forms[ $short ][5], intval($this->_seconds))
			); // seconds
		}

		return trim(preg_replace('/ {2,}/', ' ', join(' ', is_infinite($max_groups) ? $for_print : array_slice($for_print, 0, $max_groups))));
	}

	public function __toString()
	{
		$days = (($this->_years * 12) + $this->_months) * 30 + $this->_days;
		$days = ($days > 0) ? $days . MeasureWord::getFormByForms($this->_forms[1][2], $days) . ' ' : '';

		return sprintf("%s%02d:%02d:%02d", $days, $this->_hours, $this->_minutes, $this->_seconds);
	}
}