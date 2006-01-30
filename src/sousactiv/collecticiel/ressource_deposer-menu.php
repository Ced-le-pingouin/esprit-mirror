<?php

require_once("globals.inc.php");

$sMenu = NULL;
$aMenus = NULL;
$sBlockHead = NULL;

if (isset($HTTP_GET_VARS["menu"]))
	switch ($HTTP_GET_VARS["menu"])
	{
		case "deposer":
			$aMenus = array(
				array("Déposer","top.frames['Principale'].envoyer();"),
				array("Annuler","top.close();")
			);
			break;
			
		case "reessayer":
			$aMenus = array(
				array("Retour","top.location.replace('ressource_deposer-index.php');"),
				array("Annuler","top.close();")
			);
			break;
			
		default:
			$aMenus = array(array("Fermer","top.close();"));
			break;
	}

require_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

