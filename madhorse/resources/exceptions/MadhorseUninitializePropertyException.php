<?php
namespace madHorse\resources\exceptions;

class MadhorseUninitializePropertyException extends MadhorseException 
{
	function __construct($message="")
	{
		parent::__construct($message);
	}
}