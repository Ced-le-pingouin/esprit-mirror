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

/*
** Fichier ................: equipe.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 17/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// ---------------------
$sCorpHtml = NULL;

$sCorpFonctionInit = NULL;

if (count($_POST) > 0)
{
	include_once(dir_database("ids.class.php"));
	
	$sAction = (empty($_POST["ACTION"]) ? NULL : $_POST["ACTION"]);
	$sNomEquipe = (empty($_POST["NOM_EQUIPE"]) ? NULL : $_POST["NOM_EQUIPE"]);
	$iIdEquipe = (empty($_POST["ID_EQUIPE"]) ? NULL : $_POST["ID_EQUIPE"]);
	$iNiveau = (empty($_POST["NIVEAU"]) ? TYPE_FORMATION : $_POST["NIVEAU"]);
	$iIdNiveau = (empty($_POST["ID_NIVEAU"]) ? $oProjet->oFormationCourante->retId() : $_POST["ID_NIVEAU"]);
	
	$oEquipe = new CEquipe($oProjet->oBdd,$iIdEquipe);
	
	$oIds = new CIds($oProjet->oBdd,$iNiveau,$iIdNiveau);
	
	switch ($sAction)
	{
		case "ajout":
		//   -------
			$oEquipe->defNom($sNomEquipe);
			$oEquipe->defIdFormation($oIds->retIdForm());
			$oEquipe->defIdModule($oIds->retIdMod());
			$oEquipe->defIdRubrique($oIds->retIdRubrique());
			$oEquipe->ajouter();
			
			$iIdEquipe = $oEquipe->retId();
			break;
			
		case "modif":
		//   -------
			$oEquipe->defNom($sNomEquipe);
			$oEquipe->sauvegarder();
			break;
			
		case "sup":
		//   -----
			$oEquipe->verrouillerTables();
			$oEquipe->effacer();
			$oProjet->oBdd->deverrouillerTables();
			
			$iIdEquipe = 0;
			
			break;
	}
	
	// Mettre à jour la liste des équipes et
	// fermer directement cette fenêtre
	echo "<html>\n"
		."<head>\n"
	        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
		."<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."top.opener.valider('$iIdEquipe');\ntop.close();\n"
		."//-->\n"
		."</script>\n"
		."</head>\n"
		."</html>\n";
	
	exit();
}
else
{
	$sAction   = (empty($_GET["ACTION"]) ? NULL : $_GET["ACTION"]);
	$iIdEquipe = (empty($_GET["ID_EQUIPE"]) || $sAction == "ajout" ? 0 : $_GET["ID_EQUIPE"]);
	$iNiveau   = (empty($_GET["NIVEAU"]) ? TYPE_FORMATION : $_GET["NIVEAU"]);
	$iIdNiveau = (empty($_GET["ID_NIVEAU"]) ? $oProjet->oFormationCourante->retId() : $_GET["ID_NIVEAU"]);
	
	if ($sAction != "sup")
	{
		$asTexte = array( "modif" => "Entrez le nouveau nom de l'&eacute;quipe",
		"ajout" => "Entrez un nouveau nom d'&eacute;quipe");
		
		$oEquipe = new CEquipe($oProjet->oBdd,$iIdEquipe);
		
		$sCorpHtml .= "<span class=\"intitule\">".$asTexte[$sAction]."&nbsp;:</span>\n"
			."<br>\n"
			."<input type=\"text\" name=\"NOM_EQUIPE\" id=\"id_nom_equipe\" size=\"45\" maxlength=\"50\" style=\"width: 100%;\" value=\"".$oEquipe->retNom("html")."\">\n"
			."<br>\n"
			."<div style=\"text-align: right;\">"
			."<a href=\"javascript: void(0);\" onclick=\"document.forms[0].elements['NOM_EQUIPE'].value='';document.getElementById('id_nom_equipe').focus();\">Effacer</a>"
			."</div>";
	}
	else
	{
		$oEquipe = new CEquipe($oProjet->oBdd,$iIdEquipe);
		
/*
		if ($oProjet->oFormationCourante->verifEquipe($iIdEquipe))
		{
			$sCorpHtml = "<div align=\"center\">"
				."<span class=\"Cellule_Sous_Titre\">Vous n'avez pas le droit d'effacer cette &eacute;quipe&nbsp;:</span>"
				."<p>Un ou plusieurs membres de cette &eacute;quipe a d&eacute;pos&eacute; un ou plusieurs documents.</p>"
				."</div>";
			
			$sCorpFonctionInit = "\n\ttop.oMenu().location = \"equipe_menu.php\";\n";
		}
		else
		{
*/		
			$sCorpHtml .= "<div align=\"center\">"
				."<b>Attention</b>,"
				." vous &ecirc;tes sur le point de supprimer l'&eacute;quipe"
				."<br>&laquo;&nbsp;"
				."<span class=\"Cellule_Sous_Titre\">".$oEquipe->retNom()."</span>"
				."&nbsp;&raquo;."
				."<p class=\"attention\">Voulez-vous continuer&nbsp;?</p>"
				."</div>\n";
//		}
	}
	
	if (!isset($sCorpFonctionInit))
		$sCorpHtml .= "<input type=\"hidden\" name=\"ID_EQUIPE\" value=\"".$oEquipe->retId()."\">\n"
			."<input type=\"hidden\" name=\"ACTION\" value=\"{$sAction}\">\n"
			."<input type=\"hidden\" name=\"NIVEAU\" value=\"{$iNiveau}\">\n"
			."<input type=\"hidden\" name=\"ID_NIVEAU\" value=\"{$iIdNiveau}\">\n";

}

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--
function init() {<?php echo $sCorpFonctionInit?>}
function valider() { document.forms[0].submit(); }
//-->
</script>
</head>
<body onload="init()">
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr>
<td><img src="<?php echo dir_theme_commun("icones/16x16/equipe.gif")?>" border="0"></td>
<td width="99%"><?php echo $sCorpHtml?></td>
</tr>
</table>
</form>
</body>
</html>
