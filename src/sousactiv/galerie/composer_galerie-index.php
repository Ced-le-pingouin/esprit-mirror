<?php

/*
** Fichier ................: composer_galerie-index.php
** Description ............:
** Date de cr�ation .......: 12/09/2005
** Derni�re modification ..: 06/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("galerie.lang"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdSA = (empty($HTTP_GET_VARS["idSA"]) ? NULL : $HTTP_GET_VARS["idSA"]);

$sParamsUrl = "?idSA={$url_iIdSA}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = TXT_COMPOSER_SA_GALERIE_TITRE;
$sSousTitre = $oProjet->oSousActivCourante->retNom();

// {{{ Ins�rer ces lignes dans l'en-t�te de la page html
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
<frameset rows="30px,*" frameborder="0" border="0">
<frame name="Filtre" src="composer_galerie-filtre.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="composer_galerie.php{$sParamsUrl}" frameborder="0" marginwidth="10" marginheight="10" scrolling="auto" noresize="noresize">
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
$oTpl->remplacer("{frame_src_haut}","composer_galerie-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","composer_galerie-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

