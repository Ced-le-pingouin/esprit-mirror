<?php

/*
** Fichier ................: tableau_bord-index.php
** Description ............:
** Date de cr�ation .......: 23/06/2005
** Derni�re modification ..: 09/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("tableau_bord.lang"));

$oProjet = new CProjet();

$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdForm     = (empty($HTTP_GET_VARS["form"]) ? $oProjet->oFormationCourante->retId() : $HTTP_GET_VARS["form"]);
$url_iIdModalite = (empty($HTTP_GET_VARS["idModal"]) ? NULL : $HTTP_GET_VARS["idModal"]); // !!! Laisser NULL car 0 = chat public et 1 = chat par �quipe

$sParamsUrl = "?form={$url_iIdForm}";

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);

$sTitrePrincipal = TITRE;
$sSousTitre = $oFormation->retNom();

unset($oFormation);

// {{{ Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oFiltre() { return top.frames["Filtre"]; }
function oPrincipale() { return top.frames["Principale"]; }
function rafraichir() { oFiltre().document.forms[0].submit(); }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$iHauteurFrameFiltre = (STATUT_PERS_ETUDIANT > $g_iIdStatutUtilisateur && empty($url_iIdModalite) ? 50 : 1);

$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frameset rows="{$iHauteurFrameFiltre}px,*" frameborder="0" border="0">
<frame name="Filtre" src="tableau_bord-filtre.php{$sParamsUrl}" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
<frame name="Principale" src="" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
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
$oTpl->remplacer("{frame_src_haut}","tableau_bord-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","tableau_bord-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

