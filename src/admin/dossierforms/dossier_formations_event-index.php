<?php

/*
** Fichier ................: dossier_formations_event-index.php
** Description ............:
** Date de création .......: 24/05/2005
** Dernière modification ..: 03/10/2005
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
$url_sParams = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$url_sParams .= (isset($url_sParams) ? "&" : "?")
		."{$sCle}={$sValeur}";

$url_sEvent = (empty($HTTP_GET_VARS["event"]) ? NULL : $HTTP_GET_VARS["event"]);

// ---------------------
// Initialiser
// ---------------------
if ("ajout" == $url_sEvent)
	$sTitrePrincipal = "Ajouter un dossier de formations";
else if ("modif" == $url_sEvent)
	$sTitrePrincipal = "Modifier le dossier de formations";
else if ("supp" == $url_sEvent)
	$sTitrePrincipal = "Supprimer le dossier de formations";

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="dossier_formations_event.php{$url_sParams}" frameborder="0" marginwidth="10" marginheight="20" scrolling="no" noresize="noresize">
BLOCK_FRAME_PRINCIPALE;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHtmlHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHtmlHead->ajouter($sBlockHtmlHead);
$oBlockHtmlHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","dossier_formations_event-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","dossier_formations_event-menu.php{$url_sParams}");

$oTpl->afficher();

?>

