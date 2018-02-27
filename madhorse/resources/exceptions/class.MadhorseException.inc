<?php
namespace madHorse\resources\exception;

class MadhorseException extends Exception
{
	function __construct($message="")
	{
		parent::__construct($message);
		if($message!='')
			$this->recordLog();
	}
		
	function recordLog()
	{
		$log_content = date('m/d/Y h:i:s a', time())." - ".$_SERVER['SERVER_ADDR']." - ".$_SERVER['HTTP_USER_AGENT']."\r\n";
		$log_content .=self::getMessage()."\r\n";
		$i=1;
		
		foreach(self::getTrace() as $e)
		{
			if(isset($e['function']) && isset($e['line']))
				$log_content .=$i++.". ".$e['function']."in file ".$e['file']." at line ".$e['line']."\r\n";	
		}
		$log_content .="\r\n";
		error_log($log_content, 3, "logs/error.log");
	}
	
	function showErrorDetails()
	{ 
		$output_error = "<div style='width:100%;'>";
		$output_error .= "<div style='width:100%;height:30px;background:#F57900;font-weight:bold;padding-top:5px;'>";
		$output_error .= "<span style='width:100%;height:20px;color:yellow;margin:5px;'>(!)</span>";
		$output_error .= "Notice:".self::getMessage();
		$output_error .= "</div>";

		$output_error .= "<div style='width:100%;height:20px;background:#E9B96E;font-weight:bold;padding:5px 0 5px 0;'>Call Stack</div>";

		$output_error .= "<table border=1 style='width:100%;background:#EEEEEC;'>";
		$output_error .= "<tr><td align=center><b>#</b></td><td><b>Function</b></td><td><b>Location</b></td><td><b>Line number</b></td></tr>";

		$i=1;
		foreach(self::getTrace() as $e)
		{
			if(isset($e['function']) && isset($e['line']))
			{
				$output_error .= "<tr>";
				$output_error .= "<td align=center>".$i++."</td>";
				$output_error .= "<td>".$e['function']."</td>";
				$output_error .= "<td>".$e['file']."</td>";
				$output_error .= "<td>".$e['line']."</td>";	
				$output_error .= "</tr>";
			}		  
		}

		$output_error .= "</table>";
		$output_error .= "</div>";

		echo $output_error;
	}
}