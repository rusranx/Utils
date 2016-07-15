<?php
/*
 * Timer
 * @project RusranUtils
 *
 * @author Yuri Ashurkov (rusranx)
 */

namespace RusranUtils;


class Timer
{
	static protected $start,
		$prev,
		$lang = 'ru';
	static function getDesc($type)
	{
		if($type == 'curr'){
			if(self::$lang == 'ru')
				return '<br>Скрипт выполнялся: %s<br>';
			if(self::$lang == 'en')
				return '<br>Script execution time: %s<br>';
		}
		if($type == 'prev'){
			if(self::$lang == 'ru')
				return '<br>С последнего запроса прошло: %s<br>';
			if(self::$lang == 'en')
				return '<br>Last query execution time: %s<br>';
		}
		return '<br>%s<br>';
	}
	static function reset()
	{
		self::$start = self::$prev = microtime(true);
	}
	static function currExecTime()
	{
		if(!self::$start)
			self::reset();
		self::$prev = microtime(true);
		return self::$prev - self::$start;
	}
	static function prevExecTime()
	{
		if(!self::$start)
			self::reset();
		$now = microtime(true);
		$time = $now - self::$prev;
		self::$prev = $now;
		return $time;
	}
	static function output($type = 'curr')
	{
		$exec = 0;
		if($type == 'curr')
			$exec = self::currExecTime();
		if($type == 'prev')
			$exec = self::prevExecTime();
		$exec_text = ($exec < 1.0) ?
			sprintf("%.0F ms", $exec*1000) :
			sprintf("%.01F sec", $exec);
		return sprintf(self::getDesc($type), $exec_text);
	}
	static function outputPrev()
	{
		return self::output('prev');
	}
}

?>