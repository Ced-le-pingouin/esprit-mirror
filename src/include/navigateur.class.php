<?php

class CNavigateur
{
	var $netscape6;
	
	function CNavigateur ()
	{
		global $HTTP_USER_AGENT;
		
		$this->netscape6 = eregi("netscape6",$HTTP_USER_AGENT);
	}
	
	function Netscape6 ()
	{
		return $this->netscape6;
	}
}

?>
