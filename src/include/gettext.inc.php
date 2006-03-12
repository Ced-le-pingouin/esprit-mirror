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

// ---------------------
// fonctions factices au cas où gettext n'est pas installé sur la machine, ou activé en PHP (sous Windows par exemple).
// Alors la plate-forme reste en français
// ---------------------
if (!function_exists('gettext'))
{
	function bindtextdomain($domain, $directory) { ; }
	function textdomain($domain) { ; }
	function gettext($message) { return $message; }
	function _($message) { return $message; }
}

// ---------------------
// définition de la langue
// ---------------------

{
	$lang = 'fr_FR';
	$langWin = 'FRA'; // seulement pour Windows, qui supporte comme locales les codes ISO-Alpha-3 décrits ici : http://www.unicode.org/onlinedat/countries.html

	putenv("LANG=$lang"); // optionnel
	if (!setlocale(LC_ALL, $lang)) {
		if (!setlocale(LC_ALL, $langWin)) {
			print "Erreur avec setlocale !";
		}
	} 

	$domain = 'messages';
	bindtextdomain($domain, dir_root_plateform("locale")); 
	textdomain($domain);
}

?>