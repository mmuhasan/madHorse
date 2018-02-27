<?php

use PHPUnit\Framework\TestCase;
use madHorse\router;
use madHorse\resources\domain\entity;

class entityTest extends TestCase
{
    public function testTrueIsTrue()
    {
        $foo = true;
        $this->assertTrue($foo);

        $user = new router("anc");

        $res = $user->returnOne(); 

        $this->assertEquals($res,1);

	
	$u = new entity();

	$u->name ="a";
	$r = $u->isModified();

	$this->assertTrue($r);

    }
}
