<?php
/**
 * Php Debug Beautifier
 */

class PDB
{
	public static string $filePath        = ''; // example __DIR__ . '/'
	public static string $fileName        = 'DebugLog.txt';
	public static string $backgroundColor = '#ff0000';
	public static string $textColor       = '#FFFFFFFF';

	/**
	 * @param $filePath
	 */
	public static function setFilePath(string $filePath): void
	{
		self::$filePath = $filePath;
	}

	/**
	 * @param $backgroundColor
	 */
	public static function setBackgroundColor(string $backgroundColor): void
	{
		self::$backgroundColor = $backgroundColor;
	}

	/**
	 * @param $textColor
	 */
	public static function setTextColor(string $textColor): void
	{
		self::$filePath = $textColor;
	}

	/**
	 * @param $fileName
	 */
	public static function setFileName(string $fileName): void
	{
		self::$fileName = $fileName;
	}

	/**
	 * @param $obj
	 */
	public static function show($obj): void
	{
		$args = func_get_args();
		$str  = "<div style='border:3px solid black; padding:7px; margin:3px; background-color:" . self::$backgroundColor . "; color:" . self::$textColor . "; font-size:18px;'>\n";
		$str  .= self::showLine(0);
		foreach ($args as $arg)
		{
			$str .= "'" . print_r($arg, true) . "'\n";
		}
		$str .= "</div>";

		echo "<pre>" . $str . "</pre>";
	}

	/**
	 * @param $obj
	 */
	public static function showAndDie($obj): void
	{
		$args = func_get_args();
		$str  = "<div style='border:3px solid black; padding:7px; margin:3px; background-color:" . self::$backgroundColor . "; color:" . self::$textColor . "; font-size:18px;'>\n";
		$str  .= "<h4>Application has died at " . self::showLine(0) . "</h4>";
		foreach ($args as $arg)
		{
			$str .= "'" . print_r($arg, true) . "'\n";
		}
		$str .= "</div>";

		echo "<pre>" . $str . "</pre>";
		die();
	}

	/**
	 * @param $obj
	 *
	 * @throws \Exception
	 */
	public static function log($obj): void
	{
		if (self::$filePath == '')
		{
			throw new Exception("You must define a file path to log to Debug::setFilePath()");
		}
		elseif (!is_dir(self::$filePath))
		{
			throw new Exception("The filePath must be a valid directory");
		}

		$args    = func_get_args();
		$message = "";
		foreach ($args as $arg)
		{
			$message .= "'" . print_r($arg, true) . "'\n";
		}
		$file = self::showLine(0);
		$file = str_replace(self::$filePath, '', $file);

		$now     = date("F j, Y, G:i:s");
		$message = "pid:" . getmypid() . " | {$now}: [{$file}] {$message}";

		$fp = fopen(self::$filePath . self::$fileName, "a");
		fwrite($fp, $message);
		fclose($fp);
	}

	/**
	 * @param int $offset
	 *
	 * @return string
	 */
	public static function showLine(int $offset = 0): string
	{
		$entries = debug_backtrace();
		if (count($entries) < $offset + 3)
		{
			return "";
		}
		$fileName     = $entries[$offset + 1]['file'];
		$lineNumber   = $entries[$offset + 1]['line'];
		$functionName = $entries[$offset + 2]['function'];

		return "{$fileName}:{$lineNumber} {$functionName}()";
	}

	/**
	 * @throws \Exception
	 */
	public static function showStack()
	{
		self::log(self::showLine(0), self::showLine(1), self::showLine(2), self::showLine(3), self::showLine(4), self::showLine(5));
	}
}