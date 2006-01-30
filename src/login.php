<?php

/*
** Fichier ................: login.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 12/07/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iCodeEtat = (empty($HTTP_GET_VARS["codeEtat"]) ? 0 : $HTTP_GET_VARS["codeEtat"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("login/login.tpl",FALSE,TRUE));
$oBlocErreurLogin = new TPL_Block("BLOCK_ERREUR_LOGIN",$oTpl);
$oBlocAvertissementLogin = new TPL_Block("BLOCK_AVERTISSEMENT_LOGIN",$oTpl);

//  {{{ Afficher un message d'erreur lorsque le pseudo ou le mot de passe de
//      l'utilisateur est incorrect
if ($url_iCodeEtat > 0)
	$oBlocErreurLogin->afficher();
else
	$oBlocErreurLogin->effacer();
// }}}

// {{{ Afficher un message d'avertissement
$sRequeteSql = "SELECT AvertissementLogin FROM Projet LIMIT 1";
$hResult = $oProjet->oBdd->executerRequete($sRequeteSql);
$oEnreg = $oProjet->oBdd->retEnregSuiv($hResult);
$sAvertissementLogin = $oEnreg->AvertissementLogin;
$oProjet->oBdd->libererResult($hResult);

if (strlen($sAvertissementLogin))
{
	$oBlocAvertissementLogin->remplacer("{login.avertissement}",convertBaliseMetaVersHtml($sAvertissementLogin));
	$oBlocAvertissementLogin->afficher();
}
else
 $oBlocAvertissementLogin->effacer();
// }}}

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"), array("<form name=\"formulId\" action=\"index2.php\" method=\"post\" target=\"_top\">","</form>"));
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

