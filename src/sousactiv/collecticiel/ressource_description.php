<?php

require_once("globals.inc.php");

require_once(dir_database("bdd.class.php"));

$sDescr = NULL;

if (isset($HTTP_GET_VARS["idResSA"]) && $HTTP_GET_VARS["idResSA"] > 0)
{
	$oBdd = new CBdd();
	$oRes = new CRessourceSousActiv($oBdd,$HTTP_GET_VARS["idResSA"]);
	$sDescr = $oRes->retDescr();
	$oBdd->terminer();
}

?>
<html>
<head><?php inserer_feuille_style(); ?></head>
<body>
<p><?php echo (isset($sDescr) ? htmlentities($sDescr) : "Ce fichier ne contient pas de description !"); ?></p>
</body>
</html>
