<?php

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialisation
// ---------------------
$sParamsUrl = "?idPers=".$oProjet->retIdUtilisateur()
	."&idResSA=".(empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "ressource_evaluation-titre.php";
$sTitrePrincipal = "Evaluation du document";

// ---------------------
// Frame principal
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frameset rows="23,1,*" frameborder="0" border="0">
<frame name="tuteurs" src="ressource_evaluation-tuteurs.php{$sParamsUrl}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="sep1" src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "ressource_evaluation-menu.php";
$sNomFichierIndex = "dialog-index.tpl";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

