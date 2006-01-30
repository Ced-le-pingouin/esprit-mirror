<?php

/*
** Fichier ................: theme.global.php
** Description ............: Intègre les éléments globals à propos du design de 
**                           la plate-forme et insére les thèmes que 
**                           l'utilisateur a choisi.
** Date de création .......: 10-07-2001
** Dernière modification ..: 06-04-2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

function lien_feuille_style ($v_sFichierAInclure)
{
	return "<link"
		." type=\"text/css\""
		." rel=\"stylesheet\""
		." href=\"".dir_theme(trim($v_sFichierAInclure),FALSE,FALSE)."\""
		.">\n";
}

function inserer_feuille_style ($v_asFichiersCSS=NULL,$v_bAfficher=TRUE)
{
	// Le fichier "globals.css" est le premier feuille de style à afficher
	$sLienFichiersCSS = lien_feuille_style("globals.css");
	
	if ($v_asFichiersCSS != NULL)
		foreach(explode(";",trim($v_asFichiersCSS)) as $sFeuilleDeStyle)
			$sLienFichiersCSS .= lien_feuille_style($sFeuilleDeStyle);
	
	if ($v_bAfficher) echo $sLienFichiersCSS; else return $sLienFichiersCSS;
}

?>
