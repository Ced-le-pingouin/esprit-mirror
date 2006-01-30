<?php

/*
** Fichier ................: glossaire_composer-index.php
** Description ............:
** Date de création .......: 28/07/2004
** Dernière modification ..: 28/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->terminer();

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Composer un glossaire";

$sFramePrincipale = <<<EOF_FRAME_PRINCIPALE
<frameset cols="210,*">
<frame name="Liste" src="glossaire_composer-liste.php" frameborder="0" scrolling="auto" noresize="noresize">
<frameset rows="*,23">
<frame name="Principale" src="glossaire_composer.php" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="SousMenu" src="glossaire_composer-sous_menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</frameset>
EOF_FRAME_PRINCIPALE;

$sBlocHead = <<<EOF_BLOC_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
function oSousMenu() { return top.frames["SousMenu"]; }
//-->
</script>
EOF_BLOC_HEAD;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlocHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlocHead->ajouter($sBlocHead);
$oBlocHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","glossaire_composer-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","glossaire_composer-menu.php");

$oTpl->afficher();

?>
