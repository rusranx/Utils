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
		$_unit = [
		["год", "года", "лет", "г", "г", "л"],
		["месяц", "месяца", "месяцев", "мес", "мес", "мес"],
		["день", "дня", "дней", "д", "д", "д"],
		["час", "часа", "часов", "ч", "ч", "ч"],
		["минута", "минуты", "минут", "мин", "мин", "мин"],
		["секунда", "секунды", "секунд", "сек", "сек", "сек"],
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

	private function _morph($n, $f1, $f2, $f5)
	{
		$n = abs(intval($n)) % 100;
		if ($n > 10 && $n < 20) return $f5;
		$n = $n % 10;
		if ($n > 1 && $n < 5) return $f2;
		if ($n == 1) return $f1;

		return $f5;
	}

	public function getUnixtime()
	{
		return $this->_unixtime;
	}

	public function getHuman($max_groups = INF, $short = false)
	{
		if ($max_groups < 1) $max_groups = INF;
		$for_print = [];

		if ($this->_years > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_years,
				(!$short)
					? $this->_morph(intval($this->_years), $this->_unit[0][0], $this->_unit[0][1], $this->_unit[0][2])
					: $this->_morph(intval($this->_years), $this->_unit[0][3], $this->_unit[0][4], $this->_unit[0][5])
			); // years
		}

		if ($this->_months > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_months,
				(!$short)
					? $this->_morph(intval($this->_months), $this->_unit[1][0], $this->_unit[1][1], $this->_unit[1][2])
					: $this->_morph(intval($this->_years), $this->_unit[1][3], $this->_unit[1][4], $this->_unit[1][5])
			); // months
		}

		if ($this->_days > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_days,
				(!$short)
					? $this->_morph(intval($this->_days), $this->_unit[2][0], $this->_unit[2][1], $this->_unit[2][2])
					: $this->_morph(intval($this->_years), $this->_unit[2][3], $this->_unit[2][4], $this->_unit[2][5])
			); // days
		}

		if ($this->_hours > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_hours,
				(!$short)
					? $this->_morph(intval($this->_hours), $this->_unit[3][0], $this->_unit[3][1], $this->_unit[3][2])
					: $this->_morph(intval($this->_years), $this->_unit[3][3], $this->_unit[3][4], $this->_unit[3][5])
			); // hours
		}

		if ($this->_minutes > 0) {
			$for_print[] = sprintf("%d %s",
				$this->_minutes,
				(!$short)
					? $this->_morph(intval($this->_minutes), $this->_unit[4][0], $this->_unit[4][1], $this->_unit[4][2])
					: $this->_morph(intval($this->_years), $this->_unit[4][3], $this->_unit[4][4], $this->_unit[4][5])
			); // minutes
		}

		if ($this->_seconds > 0 || empty($for_print)) {
			$for_print[] = sprintf("%d %s",
				$this->_seconds,
				(!$short)
					? $this->_morph(intval($this->_seconds), $this->_unit[5][0], $this->_unit[5][1], $this->_unit[5][2])
					: $this->_morph(intval($this->_years), $this->_unit[5][3], $this->_unit[5][4], $this->_unit[5][5])
			); // seconds
		}

		return trim(preg_replace('/ {2,}/', ' ', join(' ', is_infinite($max_groups) ? $for_print : array_slice($for_print, 0, $max_groups))));
	}

	public function __toString()
	{
		$days = (($this->_years * 12) + $this->_months) * 30 + $this->_days;
		$days = ($days > 0) ? $days . 'д ' : '';

		return sprintf("%s%02d:%02d:%02d", $days, $this->_hours, $this->_minutes, $this->_seconds);
	}
}