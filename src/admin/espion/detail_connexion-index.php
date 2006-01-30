<?php

require_once("globals.inc.php");

// ---------------------
// Initialisation
// ---------------------

$sParamUrl = (isset($HTTP_GET_VARS["idPers"]) ? "?idPers=".$HTTP_GET_VARS["idPers"] : NULL);

// ---------------------
// Frame du Titre
// ---------------------
$sTitrePrincipal = "Détails de connexion";
$sFrameSrcTitre = "detail_connexion-titre.php";

// ---------------------
// Frame principal
// ---------------------

// Fichier d'en-tête de la page html
$sBlockHead =<<< BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function recharger() { top.location = top.location; }
//-->
</script>
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = "<frame"
	." src=\"detail_connexion.php{$sParamUrl}\""
	." marginwidth=\"2\" marginheight=\"2\""
	." name=\"Principale\""
	." scrolling=\"yes\">";

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "detail_connexion-menu.php";

$sNomFichierIndex = "dialog-index.tpl";

require_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
