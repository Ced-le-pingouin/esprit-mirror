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

require_once("globals.inc.php");
$oProjet = new CProjet();
//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($_GET))
{
	$v_iIdFormulaire = $_GET['idformulaire'];
}
else if (isset($_POST))
{
	$v_iIdFormulaire = $_POST['idformulaire'];
}
else
{
	$v_iIdFormulaire = 0;
}

/*
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
//FIN CSS
echo "</head>\n";

echo "<body class=\"popup\" onLoad=\"self.focus()\">"; // ici onLoad permet d'afficher la popup au dessus de toutes les autres pages
*/


if (isset($_GET['valider']))
{
	$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
	$oFormulaire_Axe->effacerAxesForm($v_iIdFormulaire);
	
	$sListeAxes="";  //Va contenir une liste des axes du formulaire [variable string avec les valeurs séparées par des virgules]
	$axes = $_REQUEST['axes'];
	for ($i = 0; $i < count($axes); $i++)
	{
		//echo "<br>Axe numéro ".$axes[$i]." sélectionné.";
		$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd,$v_iIdFormulaire,$axes[$i]);
		$oFormulaire_Axe->ajouter();
		$sListeAxes.="$axes[$i]".",";
	}
	
	//Ci-dessous : suppression de la virgule de trop a la fin de la chaîne de caractères
	$sListeAxes = subStr($sListeAxes,0,strlen($sListeAxes)-1);
	
	$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
	$oReponse_Axe->VerifierValidite($v_iIdFormulaire,$sListeAxes);
	
	$iNbAxesForm = count($axes);
	if ($iNbAxesForm < 1)
	{
		echo "<p align=\"center\">";
		echo "<br><b>Les changements ont bien été enregistrés</b>";
		echo "<br>Aucun Axe n'a été selectionné pour ce formulaire";
		echo "</p>";
	}
	else
	{
		echo "<p align=\"center\">";
		echo "<br><b>Les changements ont bien été enregistrés</b>";
		echo "<br>Nombre d'axe sélectionné au total : ".$iNbAxesForm;
		echo "</p>";
		
		$oTpl = new Template("formulaire_axe_2.tpl");
		
		$oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
					
		if (TRUE)
		{
			$oBlock->beginLoop();
			
			for ($i = 0; $i < count($axes); $i++)
			{
				$oBlock->nextLoop();
				$oAxe = new CAxe($oProjet->oBdd,$axes[$i]); //Crée un objet objet axe
				
				$oBlock->remplacer("{id_axe}",$oAxe->retId());
				$oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
			}
			
			$oBlock->afficher();
		}
		else
		{
			$oBlock->effacer();
		}
					
		$oTpl->afficher();	  
		$oProjet->terminer();  //Ferme la connection avec la base de données
	}
}
else
{
	//Ceci met dans un tableau les Id des axes contenu dans le formulaire
	$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
	$TabAxesForm = $oFormulaire_Axe->AxesDsFormulaire($v_iIdFormulaire);
	
	$hResult = $oProjet->oBdd->executerRequete("SELECT * FROM Axe order by IdAxe");
	
	$oTpl = new Template("formulaire_axe_1.tpl");
	$oTpl->remplacer("{chemin_windows.js}",dir_javascript("window.js"));
	$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
	
	$oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
	
	if (TRUE)
	{
		$oBlock->beginLoop();
		
		while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
		{
			$oBlock->nextLoop();
			$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
			$oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
			
			$oBlock->remplacer("{id_axe}",$oAxe->retId());
			$oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
			
			//Permet de cocher la checkbox si l'axe en cours de traitement est présent dans le formulaire
			if(in_array($oAxe->retId(), $TabAxesForm)) 
			{
				$oBlock->remplacer("{chk}","CHECKED");
				$oBlock->remplacer("{couleur_police1}","<i>");
				$oBlock->remplacer("{couleur_police2}","</i>");
			}
			else
			{
				$oBlock->remplacer("{chk}","");
				$oBlock->remplacer("{couleur_police1}","");
				$oBlock->remplacer("{couleur_police2}","");
			}
		}
		
		$oBlock->afficher();
	}
	else
	{
		$oBlock->effacer();
	}
	
	$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
	
	$oTpl->afficher();	  
	$oProjet->oBdd->libererResult($hResult);
	$oProjet->terminer();  //Ferme la connection avec la base de données
}
//echo "</body>\n";
//echo "</html>\n";
?>
