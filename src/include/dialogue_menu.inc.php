<?php

require_once("globals.inc.php"); 

$sLigneMenu = NULL;

function dialogue_ajouter_element ($v_sLien,$v_sAligner=NULL,$v_sLargeurColonne=NULL)
{
	global $sLigneMenu;
	
	$sLigneMenu	.= "<td"
		.(isset($v_sLargeur) ? " width=\"{$v_sLargeurColonne}\"" : NULL)
		.">"
		.(isset($v_sAligner) ? "<div align=\"{$v_sAligner}\">{$v_sLien}</div>" : $v_sLien)
		."</td>";
}

function dialogue_afficher_menu ()
{
	global $sLigneMenu;
	
	echo "<html>"
		."<head>"
		.lierFichiersCSS("menu.css",FALSE)
		."</head>"
		."<body>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" height=\"100%\">"
		."<tr>{$sLigneMenu}</tr></table>"
		."</body>"
		."</html>";
}

?>
