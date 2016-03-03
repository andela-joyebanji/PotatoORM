<?php

abstract class A {
	function __construct()
	{
		echo "Here\n";
	}

}

class B extends A {
	
}

$b = new B(); 

