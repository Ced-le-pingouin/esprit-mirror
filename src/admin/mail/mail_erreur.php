<?php

/*
** Fichier ................: mail_erreur.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 20/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$bEnvoiCourrielReussi = (empty($HTTP_GET_VARS["erreur"]) ? TRUE : FALSE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("mail_erreur.tpl");

$sVarEnvoiCourrielReussi = $oTpl->defVariable("SET_ENVOI_COURRIEL_REUSSI");
$sVarEnvoiCourrielEchoue = $oTpl->defVariable("SET_ENVOI_COURRIEL_ECHOUE");

if ($bEnvoiCourrielReussi)
{
	$oTpl->remplacer("{envoi_courriel->message}",$sVarEnvoiCourrielReussi);
}
else
{
	$bErreurPartielle = TRUE;
	
	$oTpl->remplacer("{envoi_courriel->message}",$sVarEnvoiCourrielEchoue);
	
	$sVarErreurPartielle = $oTpl->defVariable("VAR_ERREUR_PARTIELLE");
	$sVarErreurComplete  = $oTpl->defVariable("VAR_ERREUR_COMPLETE");
	
	if ($bErreurPartielle)
		$oTpl->remplacer("{envoi_courriel->message}",$sVarErreurPartielle);
	else
		$oTpl->remplacer("{envoi_courriel->message}",$sVarErreurComplete);
}

$oTpl->afficher();

?>

