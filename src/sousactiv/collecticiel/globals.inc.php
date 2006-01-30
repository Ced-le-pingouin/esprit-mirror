<?php

/*
** Fichier ................: globals.inc.php
** Description ............: 
** Date de création .......: 28-10-2002
** Dernière modification ..: 10-12-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

// *************************************
//
// *************************************

require_once("../../globals.inc.php");

// *************************************
//
// *************************************

define("PAS_DOCUMENTS_SELECTIONNER",0);
define("TRANSFERT_REUSSI_SAUF",2);
define("TRANSFERT_ECHOUE",1);
define("TRANSFERT_REUSSI",255);

// *************************************
//
// *************************************

function collecticiel_erreur ($v_sMessageErreur)
{
	echo "<html>"
		."<head>"
		.inserer_feuille_style(NULL,FALSE)
		."</head>"
		."<body>"
		."<div align=\"center\">"
		."<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"70%\">"
		."<tr>"
		."<td>{$v_sMessageErreur}</td>"
		."</tr>"
		."</table>"
		."</div>"
		."</body>"
		."</html>";
}

function creer_repertoire ($v_sNomRepertoire)
{
	$sChemin = NULL;
		
	foreach (explode("/",$v_sNomRepertoire) as $sNomRepertoire)
	{
		$sChemin .= "{$sNomRepertoire}/";
		
		if (!is_dir($sChemin))
			mkdir($sChemin,0744);
	}
}

?>
