<?php

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sTitrePrincipal = (empty($HTTP_GET_VARS["tp"]) ? "Inscription" : stripslashes(rawurldecode($HTTP_GET_VARS["tp"])));

// ---------------------
// Initialiser
// ---------------------
$sFramePrincipal = <<<BLOC_FRAME_PRINCIPALE
<frameset rows="27,1,*">
<frame src="choix_formation-filtre.php" name="Filtre" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
<frame src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frame src="choix_formation.php" name="Principal" frameborder="0" scrolling="auto" noresize="noresize">
</frameset>
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{titre_page_html}",htmlentities($url_sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","choix_formation-titre.php?tp=".rawurlencode($url_sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","choix_formation-menu.php");

$oTpl->afficher();

?>

