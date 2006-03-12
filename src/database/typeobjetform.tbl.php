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
** Fichier ................: typeobjetform.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CTypeObjetForm 
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;

 function CTypeObjetForm(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
								  //si 0 crée un objet presque vide sinon 
								  //rempli l'objet avec les données de la table TypeObjetForm
								  //de l'elément ayant l'Id passé en argument 
								  //(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
 }
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
 function init ($v_oEnregExistant=NULL)  
 {
	    if (isset($v_oEnregExistant))
	    {
				 $this->oEnregBdd = $v_oEnregExistant;
	    }
	    else
	    {
			$sRequeteSql = "SELECT *"
				." FROM TypeObjetForm"
				." WHERE IdTypeObj='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdTypeObj;
 }

 function ajouter () //Cette fonction ajoute une question de type texte court,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO TypeObjetForm SET IdTypeObj=NULL;";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de définition

 function defIdTypeObj ($v_iIdTypeObj)
{
  $this->oEnregBdd->IdTypeObj = $v_iIdTypeObj;
}


 function defNomTypeObj ($v_sNomTypeObj)
{
  $this->oEnregBdd->NomTypeObj = $v_sNomTypeObj;
}

function defDescTypeObj ($v_sDescTypeObj)
{
  $this->oEnregBdd->DescTypeObj = $v_sDescTypeObj;
}

//Fonctions de retour

function retId () { return $this->oEnregBdd->IdTypeObj; }
function retNomTypeObj () { return $this->oEnregBdd->NomTypeObj; }
function retDescTypeObj () { return $this->oEnregBdd->DescTypeObj; }

/*
function enregistrer ()
	{
	if ($this->oEnregBdd->IdObjForm !=NULL)
	   {
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
		$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
		$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
		
		//Valeur par défaut de MaxCar c'est la valeur de LargeurQTC
		if (strlen($this->oEnregBdd->MaxCarQTC) < 1) 
				{$this->oEnregBdd->MaxCarQTC = $this->oEnregBdd->LargeurQTC;}
		
		
		$sRequeteSql = "REPLACE QTexteCourt SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", EnonQTC='{$sEnonQTC}'"
			.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
			.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
			.", TxtAvQTC='{$sTxtAvQTC}'"
			.", TxtApQTC='{$sTxtApQTC}'"
			.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
			.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"; 		
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	   }
	else
	   {
	   Echo "Identifiant NULL enregistrement impossible";
	   }
	
	
	}
*/
}

?>
