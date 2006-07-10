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

if (stristr($_SERVER['HTTP_USER_AGENT'], "Opera 3"))
	$browser = "Opera 3";
else if (stristr($_SERVER['HTTP_USER_AGENT'], "Opera 4"))
	$browser = "Opera 4";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Opera 5"))
	$browser = "Opera 5";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Opera 6"))
	$browser = "Opera 6";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Opera/6"))
	$browser = "Opera 6";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Opera"))
	$browser = "Opera";

else if (stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE 6"))
	$browser = "Microsoft Internet Explorer 6";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE 5"))
	$browser = "Microsoft Internet Explorer 5";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE 4"))
	$browser = "Microsoft Internet Explorer 4";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE 3"))
	$browser = "Microsoft Internet Explorer 3";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE"))
	$browser = "Microsoft Internet Explorer";
		
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Mozilla/5"))
	$browser = "Netscape 6";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Netscape6"))
	$browser = "Netscape 6";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Mozilla/4"))
	$browser = "Netscape 4";
else if (stristr( $_SERVER['HTTP_USER_AGENT'], "Mozilla/3"))
	$browser = "Netscape 3";
else 
	$browser = "Unknown";
?>

