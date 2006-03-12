<?php
/*
** Fichier			: pas_de_cache.inc.php
** Description		: en-tête HTTP (à inclure AVANT toute sortie texte)
**					  empêchant le navigateur de placer une page dans
**					  son cache
** Création			: 31-01-2001 (Cédric FLOQUET, cedric.floquet@advalvas.be)
** Dernière modif	: 06-09-2001
**
** (c) 2001 Unité de Technologie de l'Education. Tous droits réservés.
*/


header("Expires: -1");
header("Last-Modified: -1");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
?>
