<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

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
