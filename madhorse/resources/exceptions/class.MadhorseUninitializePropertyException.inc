<?php
namespace madHorse\resources\exception;

class MadhorseUninitializePropertyException extends MadhorseException 
{
	function __construct($message="")
	{
		parent::__construct($message);
	}
}