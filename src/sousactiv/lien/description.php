<?php

/*
** Fichier ................: description.php
** Description ............:
** Date de cr�ation .......: 28/06/2004
** Derni�re modification ..: 14/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_admin("awareness","awareness.inc.php",TRUE));

$oProjet = new CProjet();
$oProjet->initStatutsUtilisateur(TRUE);

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_bEncadrer   = (empty($HTTP_GET_VARS["encadrer"]) ? FALSE : $HTTP_GET_VARS["encadrer"]);
$url_bIndirect   = (empty($HTTP_GET_VARS["indirect"]) ? FALSE : (bool)$HTTP_GET_VARS["indirect"]);

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$sStatutUtilisateur = rawurlencode($oProjet->retTexteStatutUtilisateur());

if (!$url_bIndirect)
{
	$oProjet->modifierInfosSession(SESSION_FORM,$oIds->retIdForm(),FALSE);
	$oProjet->modifierInfosSession(SESSION_MOD,$oIds->retIdMod(),FALSE);
	$oProjet->modifierInfosSession(SESSION_UNITE,$oIds->retIdRubrique(),FALSE);
	$oProjet->modifierInfosSession(SESSION_ACTIV,$oIds->retIdActiv(),FALSE);
	$oProjet->modifierInfosSession(SESSION_SOUSACTIV,$oIds->retIdSousActiv(),FALSE);
	$oProjet->enregistrerInfosSession();
}

$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript">
<!--
function init() { top.changerStatutUtilisateur('{$sStatutUtilisateur}'); }
window.onload = init;
//-->
</script>
BLOC_HTML_HEAD;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("description.tpl",FALSE,TRUE));

// Bloc d'en-t�te de la page html
$oBlocHtmlHead = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);

if (TYPE_FORMATION == $url_iTypeNiveau)
	$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function init() { top.changerStatutUtilisateur('{$sStatutUtilisateur}'); }
window.onload = init;
//-->
</script>
BLOC_HTML_HEAD;
else
	$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
BLOC_HTML_HEAD;

$oBlocHtmlHead->ajouter($sBlocHtmlHead);
$oBlocHtmlHead->afficher();

// Description
$oBlocDescription = new TPL_Block("BLOCK_DESCRIPTION",$oTpl);

// Awareness + description
$oAwareness = new TPL_Block("BLOCK_APPLET_AWARENESS",$oTpl);

$oSet_StyleDescrFormation = $oTpl->defVariable("SET_ENCADRER_DESCR_FORMATION");

$sStyleEncadrement = NULL;

switch ($url_iTypeNiveau)
{
	case TYPE_FORMATION:
		$oFormation = new CFormation($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oFormation->retNom());
		$sDescription = $oFormation->retDescr();
		$oAwareness->remplacer("{applet_awareness}",retAwarenessSpy($oFormation->retNom(),TRUE));
		$oAwareness->afficher();
		$sStyleEncadrement = " ".$oSet_StyleDescrFormation;
		unset($oFormation);
		break;
		
	case TYPE_RUBRIQUE:
		$oRubrique = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oRubrique->retNom());
		$sDescription = $oRubrique->retDescr();
		$oAwareness->effacer();
		unset($oRubrique);
		break;
		
	case TYPE_SOUS_ACTIVITE:
		$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oSousActiv->retNom());
		$sDescription = $oSousActiv->retDescr();
		$oAwareness->effacer();
		unset($oSousActiv);
		break;
		
	default:
		$sTitrePageHtml = NULL;
		$sDescription = NULL;
		$oAwareness->effacer();
}

if (isset($sDescription))
{
	$oBlocDescription->remplacer("{description.style.class}",$sStyleEncadrement);
	$oBlocDescription->remplacer("{description.texte}",convertBaliseMetaVersHtml($sDescription));
	$oBlocDescription->afficher();
}
else
	$oBlocDescription->effacer();

// {{{ Param�tres du tableau de bord
$asRechTpl = array("{tableaudebord.niveau.id}","{tableaudebord.niveau.type}");
$amReplTpl = array($oIds->retIdRubrique(),TYPE_RUBRIQUE);
$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

$oTpl->remplacer("{html.title}",$sTitrePageHtml);

$oTpl->afficher();

$oProjet->terminer();

?>

