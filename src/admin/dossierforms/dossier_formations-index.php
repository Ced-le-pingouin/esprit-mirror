<?php

/*
** Fichier ................: dossier_formations-index.php
** Description ............:
** Date de création .......: 04/04/2005
** Dernière modification ..: 03/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("dossiers.lang"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sParams = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$url_sParams .= (isset($url_sParams) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = TITRE_CREER_MODIFIER_DOSSIER;

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oListe() { return top.frames["Liste"]; }
function oPrincipale() { return top.frames["Principale"]; }
function fermer() {
	if (top.opener)
		top.opener.top.location = "changer_dossier-index.php?idDossierForms=0";
	top.close();
}

window.onunload = fermer;
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frameset cols="180,*" frameborder="0" border="0">
<frame name="Liste" src="dossier_formations-liste.php{$url_sParams}" frameborder="0" marginwidth="0" marginheight="0" scrolling="yes" noresize="noresize">
<frame name="Principale" src="" frameborder="0" marginwidth="0" marginheight="0" scrolling="yes" noresize="noresize">
</frameset>
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
$oTpl->remplacer("{frame_src_haut}","dossier_formations-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","dossier_formations-menu.php");

$oTpl->afficher();

?>

