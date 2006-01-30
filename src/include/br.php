<?php
if (stristr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera 3"))
	$browser = "Opera 3";
else if (stristr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera 4"))
	$browser = "Opera 4";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera 5"))
	$browser = "Opera 5";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera 6"))
	$browser = "Opera 6";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera/6"))
	$browser = "Opera 6";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Opera"))
	$browser = "Opera";

else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "MSIE 6"))
	$browser = "Microsoft Internet Explorer 6";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "MSIE 5"))
	$browser = "Microsoft Internet Explorer 5";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "MSIE 4"))
	$browser = "Microsoft Internet Explorer 4";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "MSIE 3"))
	$browser = "Microsoft Internet Explorer 3";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "MSIE"))
	$browser = "Microsoft Internet Explorer";
		
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla/5"))
	$browser = "Netscape 6";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Netscape6"))
	$browser = "Netscape 6";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla/4"))
	$browser = "Netscape 4";
else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla/3"))
	$browser = "Netscape 3";
else 
	$browser = "Unknown";
?>

