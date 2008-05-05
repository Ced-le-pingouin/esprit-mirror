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
if (isset($_GET['idformulaire']))
{
	$v_iIdFormulaire = $_GET['idformulaire'];
}
else 
{
	if (isset($_POST['idformulaire']))
		$v_iIdFormulaire = $_POST['idformulaire'];
	else
		$v_iIdFormulaire = 0;
}

$oTpl = new Template("formulaire_axe.tpl");
$oTpl->remplacer("{chemin_windows.js}",dir_javascript("window.js"));
$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);

$oBlockChoix = new TPL_Block("BLOCK_CHOIX",$oTpl);
$oBlockConfirm = new TPL_Block("BLOCK_CONFIRM",$oTpl);
$oBlockLien = new TPL_Block("BLOCK_LIEN",$oTpl);

if (isset($_POST['valider']) && $oProjet->verifPermission("PERM_OUTIL_FORMULAIRE"))
{
	$oBlockConfirm->afficher();
	$oBlockChoix->effacer();
	$oBlockLien->effacer();
	
	$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
	$oFormulaire_Axe->effacerAxesForm($v_iIdFormulaire);
	
	$sListeAxes="";  //Va contenir une liste des axes du formulaire [variable string avec les valeurs séparées par des virgules]
	$axes = $_REQUEST['axes'];
	for ($i = 0; $i < count($axes); $i++)
	{
		$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd,$v_iIdFormulaire,$axes[$i]);
		$oFormulaire_Axe->ajouter();
		$sListeAxes.="$axes[$i]".",";
	}
	//Ci-dessous : suppression de la virgule de trop a la fin de la chaîne de caractères
	$sListeAxes = subStr($sListeAxes,0,strlen($sListeAxes)-1);
	
	$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
	if( strlen($sListeAxes) == 0) //aucun axe selectionné
		$sListeAxes = "0";
	$oReponse_Axe->VerifierValidite($v_iIdFormulaire,$sListeAxes);
	//problème si pas d'axe selectionné!
	
	$iNbAxesForm = count($axes);
	$oBlockAxes = new TPL_Block("BLOCK_AXES",$oTpl);

	if ($iNbAxesForm < 1)
	{
		$oBlockAxes->remplacer("{desc_axe}","Aucun");
		$oBlockAxes->afficher();
	}
	else
	{
		$oBlockAxes->beginLoop();
		for ($i = 0; $i < count($axes); $i++)
		{
			$oBlockAxes->nextLoop();
			$oAxe = new CAxe($oProjet->oBdd,$axes[$i]); //Crée un objet objet axe
			$oBlockAxes->remplacer("{desc_axe}",$oAxe->retDescAxe());
		}
		$oBlockAxes->afficher();
	}
}
else
{
	$oBlockConfirm->effacer();
	$oBlockChoix->afficher();
	$oBlockLien->afficher();

	//Ceci met dans un tableau les Id des axes contenu dans le formulaire
	$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
	$TabAxesForm = $oFormulaire_Axe->AxesDsFormulaire($v_iIdFormulaire);
	
	$oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
	$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
	$aoListeAxes = $oAxe->retListeAxes();
	
	if (count($aoListeAxes)>0)// verifie s'il y a au moins un axe
	{
		$oBlock->beginLoop();
		foreach ($aoListeAxes as $oEnreg)
		{
			$oBlock->nextLoop();
			$oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
			
			$oBlock->remplacer("{id_axe}",$oAxe->retId());
			$oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
			
			//Permet de cocher la checkbox si l'axe en cours de traitement est présent dans le formulaire
			if(in_array($oAxe->retId(), $TabAxesForm)) 
			{
				$oBlock->remplacer("{chk}","checked='checked'");
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
}
$oTpl->afficher();
$oProjet->terminer();  //Ferme la connection avec la base de données
?>
