<?php

/*
** Fichier ................: tchatche_connectes.swf.php
** Description ............:
** Date de cr�ation .......: 20/01/2005
** Derni�re modification ..: 20/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$sAdresseComplete = dir_root_plateform($HTTP_POST_VARS["adresseComplete"]);

echo "&log={$sAdresseComplete}&";

if (is_file("{$sAdresseComplete}/delta_chat_58"))
{
	echo "&log=";
	$fp = fopen("{$sAdresseComplete}/delta_chat_58","r");
	fpassthru($fp);
	flush();
	echo "&";
}
?>
