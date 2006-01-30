<?php

if (is_object($oProjet) && is_object($oProjet->oUtilisateur))
{
	$sPseudo = $oProjet->oUtilisateur->retPseudo();
	
	$sInfosUtilisateur = "<span"
		." class=\"infos_utilisateur\""
		." style=\"cursor: help;\""
		." title=\"".$oProjet->retTexteUtilisateur()."\""
		.">{$sPseudo}</span>"
		.($oProjet->retStatutUtilisateur() == STATUT_PERS_VISITEUR
			? ""
			: "&nbsp;<span id=\"idStatutUtilisateur\" class=\"infos_utilisateur\">(".$oProjet->retTexteStatutUtilisateur().")</span>");
}
else
{
	$sInfosUtilisateur = "<span"
		." class=\"infos_utilisateur\""
		.">".$oProjet->retTexteStatutUtilisateur(STATUT_PERS_VISITEUR)."</span>";
}

$sInfosUtilisateur .= "&nbsp;";

?>
