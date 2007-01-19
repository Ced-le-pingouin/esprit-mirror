<?php

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Modifier les permissions";
$sSousTitre = NULL;

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
function rafraichir() { oPrincipale().location = oPrincipale().location; }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="permissions.php" frameborder="0" marginwidth="10" marginheight="10" scrolling="auto">
BLOCK_FRAME_PRINCIPALE;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHtmlHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHtmlHead->ajouter($sBlockHtmlHead);
$oBlockHtmlHead->afficher();

$oTpl->remplacer("{titre_page_html}",emb_htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","permissions-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","permissions-menu.php");
$oTpl->afficher();

$oProjet->terminer();




/**

<html>
<head><title>Modifier les permissions par statut</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<frameset rows="63,*,25" border="0" frameborder="0" framespacing="0">
<frame name="titre" src="permissions-titre.php" border="0" marginwidth="0" marginheight="0" frameborder="0" framespacing="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="permissions.php" border="0" marginwidth="0" marginheight="0" frameborder="0" framespacing="0" scrolling="yes" noresize="noresize">
<frame name="menu" src="permissions-menu.php" border="0" marginwidth="10" marginheight="4" frameborder="0" framespacing="0" scrolling="no" noresize="noresize">
</frameset>
</html>

**/
?>
