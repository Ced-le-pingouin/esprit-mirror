<?php
if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "NT 5.1")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Windows XP")) {
        $os = "Windows XP";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "NT 5")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Windows 2000")) {
        $os = "Windows 2000";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "NT")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "WinNT")) {
        $os = "Windows  NT 4";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "95")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Win95")) {
        $os = "Windows 95";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Win 9x 4.90")) {
        $os = "Windows ME";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "98")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Win98")) {
        $os = "Windows 98";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Windows 3.1")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Win16")) {
        $os = "Windows 3.x";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Macintosh")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mac") || 
	stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Macintosh;")) {
        $os = "Macintosh";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Linux")) {
        $os = "Linux";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Unix")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "sunos")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "X11")) {
        $os = "Unix";
} else if (stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "WebTV")
	|| stristr( $HTTP_SERVER_VARS['HTTP_USER_AGENT'], "AOL_TV")) {
        $os = "Web TV";
} else
        $os = "Unknown";
?>
