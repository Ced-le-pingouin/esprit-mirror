<?php

/*
** Fichier ................: forum-menu.php
** Description ............: 
** Date de cr�ation .......: 29/11/2004
** Derni�re modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));

$oProjet = new CProjet();

$iIdUtilisateur = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdForum = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);

// ---------------------
// Ins�rer ces lignes dans l'en-t�te de la page html
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript">
<!--
function copie_courriel()
{
	var iIdForum = top.oFrmListeSujets().ret_id_forum();
	var sUrl = "copie_courriel-index.php"
		+ "?idForum=" + iIdForum;
	var oWinCopieCourriel = PopupCenter(sUrl,"winCopieCourriel",370,500,"");
	oWinCopieCourriel.focus();
	return false;
}
//-->
</script>
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

$aMenus[] = array(BTN_RAFRAICHIR,"top.oFrmListeSujets().rafraichir()",1,"text-align: left; width: 1%;");
$aMenus[] = array("","",1,"text-align: left; width: 1%;");

// {{{ Copie courriel
if ($oProjet->verifPermission("PERM_COPIE_COURRIEL_FORUM"))
{
	$oForumPrefs = new CForumPrefs($oProjet->oBdd);
	$bCopieCourriel = ($oForumPrefs->initForumPrefs($url_iIdForum,$iIdUtilisateur) & $oForumPrefs->retCopieCourriel());
	$aMenus[] = array("<input type=\"checkbox\"".($bCopieCourriel ? "checked=\"checked\"" : NULL)."disabled=\"disabled\">","copie_courriel()",10,"text-align: left; width: 1%;",FALSE);
	$aMenus[] = array(str_replace(" ","&nbsp;",BTN_COPIE_COURRIEL),"copie_courriel()",20,"text-align: left; width: 1%;",FALSE);
}
// }}}

// {{{ Envoi courriel
$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
$iModaliteForum = $oForum->retModalite();

switch ($iModaliteForum)
{
	case MODALITE_POUR_TOUS: $bAfficherLienEnvoiCourriel = $oProjet->verifPermission("PERM_COURRIEL_FORUM_POUR_TOUS"); break;
	case MODALITE_PAR_EQUIPE: $bAfficherLienEnvoiCourriel = $oProjet->verifPermission("PERM_COURRIEL_FORUM_EQUIPE_ISOLEE"); break;
	case MODALITE_PAR_EQUIPE_INTERCONNECTEE: $bAfficherLienEnvoiCourriel = $oProjet->verifPermission("PERM_COURRIEL_FORUM_EQUIPE_INTERCONNECTEE"); break;
	case MODALITE_PAR_EQUIPE_COLLABORANTE: $bAfficherLienEnvoiCourriel = $oProjet->verifPermission("PERM_COURRIEL_FORUM_EQUIPE_COLLABORANTE"); break;
	default: $bAfficherLienEnvoiCourriel = FALSE;
}

unset($oForum,$iModaliteForum,$bModaliteForumParEquipe);
// }}}

if ($oProjet->verifPermission("PERM_FORUM_EXPORTER_CSV"))
	$aMenus[] = array(BTN_EXPORTER,"top.oFrmSujets().exporter()",4,"text-align: center; width: 99%;");

$aMenus[] = array(BTN_FERMER,"top.close()",99);

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

