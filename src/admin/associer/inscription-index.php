<?php

require_once("globals.inc.php");

$url_iIdForm = (isset($HTTP_GET_VARS["idform"]) ? $HTTP_GET_VARS["idform"] : 0);

$sTitrePrincipal = "Inscription";

$oProjet = new CProjet();
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);
$sNomFormation = $oFormation->retNom();
$oFormation = NULL;
$oProjet->terminer();

// ---------------------
// Template
// ---------------------
$sBlocHeadHtml = <<<BLOCK_HEAD_HTML
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>

<script type="text/javascript" language="javascript">
<!--
function choix_formation_callback(v_iIdForm)
{
	top.location = "{$HTTP_SERVER_VARS['PHP_SELF']}"
		+ "?idform=" + v_iIdForm;
}
//-->
</script>
BLOCK_HEAD_HTML;

$sFramePrincipal = "<frame"
	." src=\"inscription.php?idform={$url_iIdForm}\""
	." name=\"Principal\""
	." marginwidth=\"0\""
	." marginheight=\"0\""
	." frameborder=\"0\""
	." scrolling=\"no\""
	." noresize=\"noresize\">";

$oTpl = new Template(dir_theme("dialog-index-2.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlocHeadHtml);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities("{$sTitrePrincipal} - {$sNomFormation}"));
$oTpl->remplacer("{frame_src_haut}","inscription-titre.php?TP=".rawurlencode($sTitrePrincipal)."&ST=".rawurlencode($sNomFormation));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","inscription-menu.php?tp=".rawurlencode($sTitrePrincipal));

$oTpl->afficher();

?>

