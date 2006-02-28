<?php

// ---------------------
// fonctions factices au cas o� gettext n'est pas install� sur la machine, ou activ� en PHP (sous Windows par exemple).
// Alors la plate-forme reste en fran�ais
// ---------------------
if (!function_exists('gettext'))
{
	function bindtextdomain($domain, $directory) { ; }
	function textdomain($domain) { ; }
	function gettext($message) { return $message; }
	function _($message) { return $message; }
}

// ---------------------
// d�finition de la langue
// ---------------------

{
	$lang = 'fr_FR';
	$langWin = 'FRA'; // seulement pour Windows, qui supporte comme locales les codes ISO-Alpha-3 d�crits ici : http://www.unicode.org/onlinedat/countries.html

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