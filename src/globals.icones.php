<?php

/*
** Fichier ................: globals.icones.php
** Description ............: 
** Date de création .......: 14/02/2005
** Dernière modification ..: 13/04/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

function retLienEnvoiCourriel ($v_sParamsUrl,$v_bTemplate=TRUE)
{
	if (!$v_bTemplate) $sChemin = dir_theme_commun();
	$sLien = "<a"
		." href=\"javascript: void(0);\""
		." onclick=\"choix_courriel('{$v_sParamsUrl}'); return false;\""
		." onfocus=\"blur()\""
		." title=\"Envoyer un courriel\""
		.">"
		."<img src=\"commun://icones/24x24/courriel_envoye.gif\" width=\"24\" height=\"24\" border=\"0\">"
		."</a>";
	return ($v_bTemplate ? $sLien : str_replace("commun://",$sChemin,$sLien));
}

function retLienListeInscrits ($v_bTemplate=TRUE)
{
	if (!$v_bTemplate) $sChemin = dir_theme_commun();
	$sLien = "<a"
		." href=\"javascript: void(0);\""
		." onclick=\"liste_inscrits(); return false;\""
		." onfocus=\"blur()\""
		." title=\"Consulter la liste des inscrits\""
		.">"
		."<img src=\"commun://icones/24x24/liste_inscrits.gif\" width=\"24\" height=\"24\" border=\"0\">"
		."</a>";
	return ($v_bTemplate ? $sLien : str_replace("commun://",$sChemin,$sLien));
}

?>
