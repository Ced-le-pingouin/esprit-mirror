<?php

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (isset($HTTP_GET_VARS["idNiveau"]))
	$url_iIdParent = $HTTP_GET_VARS["idNiveau"];
else if (isset($HTTP_POST_VARS["idNiveau"]))
	$url_iIdParent = $HTTP_POST_VARS["idNiveau"];
else
	$url_iIdParent = 0;

if (isset($HTTP_GET_VARS["typeNiveau"]))
	$url_iTypeParent = $HTTP_GET_VARS["typeNiveau"];
else if (isset($HTTP_POST_VARS["typeNiveau"]))
	$url_iTypeParent = $HTTP_POST_VARS["typeNiveau"];
else
	$url_iTypeParent = 0;

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Composer les \"chat\"";

$sBlocHtmlHead =<<< BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="chat.js"></script>
<script type="text/javascript" language="javascript">
<!--
var recharger_fenetre_parente = false;
//-->
</script>
BLOCK_HTML_HEAD;

$sParamsUrl = "?idNiveau={$url_iIdParent}&typeNiveau={$url_iTypeParent}";

$sFramePrincipal =<<< BLOCK_FRAME_PRINCIPALE
<frameset cols="210,1,*" border="0" frameborder="0" framespacing="0" onunload="rafraichir_parent()">
<frame src="chat-liste.php{$sParamsUrl}" name="Liste" frameborder="0" scrolling="no" noresize="noresize">
<frame src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frameset rows="*,20" border="0" frameborder="0" framespacing="0">
<frame src="" name="Principal" frameborder="0" scrolling="auto" noresize="noresize">
<frame src="" name="SousMenu" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------

$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlocHtmlHead);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","chat-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","chat-menu.php");

$oTpl->afficher();

?>

