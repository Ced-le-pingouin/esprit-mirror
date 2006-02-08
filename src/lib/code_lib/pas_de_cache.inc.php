<?php
/*
** Fichier			: pas_de_cache.inc.php
** Description		: en-t�te HTTP (� inclure AVANT toute sortie texte)
**					  emp�chant le navigateur de placer une page dans
**					  son cache
** Cr�ation			: 31-01-2001 (C�dric FLOQUET, cedric.floquet@advalvas.be)
** Derni�re modif	: 06-09-2001
**
** (c) 2001 Unit� de Technologie de l'Education. Tous droits r�serv�s.
*/


header("Expires: -1");
header("Last-Modified: -1");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
?>
