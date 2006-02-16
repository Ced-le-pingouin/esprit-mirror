<?php

// ---------------------
// définition de la langue
// ---------------------

{
	$lang = 'fr_FR';

	putenv("LANG=$lang"); // optionnel
	if (!setlocale(LC_ALL, $lang)) {
		print "Erreur avec setlocale !";
	} 

	$domain = 'messages';
	bindtextdomain($domain, dir_root_plateform("locale")); 
	textdomain($domain);
}

?>