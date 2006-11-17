<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

$sFichierTmp = "none";

if (!empty($_FILES["FICHIER"]))
{
	$sFichierTmp         = $_FILES["FICHIER"]["tmp_name"];
	$sNomFichier         = stripslashes($_FILES["FICHIER"]["name"]);
	$sTitreFichier       = stripslashes($_POST["TITRE_FICHIER"]);
	$sDescriptionFichier = stripslashes($_POST["DESCRIPTION_FICHIER"]);
}

if ($sFichierTmp != "none")
{	
	$repDeposer = $oProjet->dir_ressources();
	
	if (!is_dir($repDeposer))
	{
 		$sRep = "";
		
		foreach (explode("/",$repDeposer) as $k)
		{
			$sRep .= $k."/";
			
			if (!file_exists($sRep))
				@mkdir($sRep,0744);
		}
	}
	
	include_once(dir_code_lib("upload.inc.php"));
	
	$dir_ressources = retNomFichierUnique($sNomFichier,$repDeposer);
	
	$err = chargerFichier($sFichierTmp,$dir_ressources);
	
	switch ($err)
	{
		case UPLOAD_EXTENSION_INTERDITE:
			
			echo "<html>\n"
				."<head>\n"
			        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
				.inserer_feuille_style()
				."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
				."function init()\n"
				."{\n"
				."\ttop.frames['Bas'].location.replace('ressource_deposer-menu.php?menu=reessayer');\n"
				."}\n"
				."//--></script>\n"
				."</head>\n"
				."<body onload=\"init()\">\n"
				."<br><br>\n"
				."<div align=\"center\"><h4>".emb_htmlentities("Ce fichier n'est pas autorisé à la copie.")."</h4></div>\n"
				."<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
				."</table>\n"
				."</body>\n"
				."</html>\n";
			
			break;
			
		case UPLOAD_ERREUR:
			
			echo "<html>\n"
				."<head>\n"
				.inserer_feuille_style()
				."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
				."function init()\n"
				."{\n"
				."\ttop.frames['Bas'].location.replace('ressource_deposer-menu.php?menu=reessayer');\n"
				."}\n"
				."//--></script>\n"
				."</head>\n"
				."<body onload=\"init()\">\n"
				."<br><br>\n"
				."<div align=\"center\"><h4>".emb_htmlentities("Le transfert du fichier a échoué.")."</h4></div>\n"
				."<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
				."</table>\n"
				."</body>\n"
				."</html>\n";
			
			break;
			
		default:
			
			// Informations à propos du document déposé
			$sNomRes = (strlen($sTitreFichier) <> 0 ? $sTitreFichier : $sNomFichier); 		// Nom de la ressource
			$sDescrRes = (strlen($sDescriptionFichier) <> 0 ? $sDescriptionFichier : "");
			$sAuteurRes = "auteur inconnu";													// Auteur
			$sUrlRes = basename($dir_ressources);											// Dans quel répertoire se touve le document
			
			$bOk = $oProjet->insererRessource($sNomRes,$sDescrRes,$sAuteurRes,stripslashes($sUrlRes));
			
			if ($bOk)
				echo "<html>"
					."<body>"
					."<script language=\"javascript\" type=\"text/javascript\">"
					."<!--\n"
					."if (top.opener && top.opener.recharger) top.opener.recharger();"
					."top.close();"
					."\n//-->"
					."</script>"
					."</body>"
					."</html>";
			else
				echo "<html>\n"
					."<head>\n"
					.inserer_feuille_style()
					."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
					."function init()\n"
					."{\n"
					."\ttop.frames['Bas'].location.replace('ressource_deposer-menu.php?menu=reessayer');\n"
					."}\n"
					."//--></script>\n"
					."</head>\n"
					."<body onload=\"init()\">\n"
					."<br><br>\n"
					."<div align=\"center\">"
					."<h4>".emb_htmlentities("Le document \"{$sNomRes}\" n'a pas pu être déposé.")."</h4>"
					."</div>\n"
					."</body>\n"
					."</html>\n";
			
			unset ($err);
	}
	
	exit();
}

?>
