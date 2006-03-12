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
** Fichier ................: axe.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 05-05-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CAxe 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	function CAxe(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
						  //si 0 crée un objet presque vide sinon 
						  //rempli l'objet avec les données de la table Axe
						  //de l'elément ayant l'Id passé en argument
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql =
				" SELECT *"
				." FROM Axe"
				." WHERE IdAxe='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter()  //Cette fonction ajoute un Objet Axe, avec tous ses champs vide, en fin de table
	{
		//$iIdAxeMax = IdMaxAxe();
		$sRequeteSql = "INSERT INTO Axe SET IdAxe=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	/*
	** Fonction 		: IdMaxAxe
	** Description		: renvoie la plus grande valeur de IdAxe de la table Axe
	** Entrée			: 
	** Sortie			:
	**					la plus grande valeur de IdAxe de la table Axe
	*/
	/*
	function IdMaxAxe()
	{
		$sRequeteSql = "SELECT MAX(IdAxe) as IdAxeMax FROM Axe";
		//echo "requete : ".$sRequeteSql;
		$hResult2=$this->oBdd->executerRequete($sRequeteSql);
		$oEnreg = $this->oBdd->retEnregSuiv($hResult2);
		$iIdAxeMax = $oEnreg->IdAxeMax;
		$this->oBdd->libererResult($hResult2);
		//echo "<br>idAxe : ".$iIdAxeMax;
		return $iIdAxeMax;
	}
	*/

	function effacer($v_bVerification = TRUE)
	{
		if ($v_bVerification)
		{
			//Avant d'effacer on vérifie que l'axe n'est utilisé par aucun formulaire
			$sRequeteSql = "Select * FROM Formulaire_Axe WHERE IdAxe ='{$this->oEnregBdd->IdAxe}'";
			$this->oBdd->executerRequete($sRequeteSql);
			$iNbFormUtiliseAxe = $this->oBdd->retNbEnregsDsResult();
			
			if ($iNbFormUtiliseAxe == 0)
			{
				$sRequeteSql = "DELETE FROM Axe WHERE IdAxe ='{$this->oEnregBdd->IdAxe}'";
				//echo "<br>effacer Axe() : ".$sRequeteSql;
				$this->oBdd->executerRequete($sRequeteSql);
				echo "<h4 align=\"center\"><br>L'axe <br>{$this->oEnregBdd->DescAxe}<br> a été supprimé</h4>";
			}
			else
			{
				echo "<h4 align=\"center\"><br>Suppression impossible car cet axe est utilisé dans : </h4>";
				
				//On recherche et affiche le nom du/des formulaire(s) où l'axe est utilisé
				$sRequeteSql =
					"  SELECT f.Titre, fa.IdAxe"
					." FROM Formulaire as f, Formulaire_Axe as fa"
					." WHERE fa.IdAxe = '{$this->oEnregBdd->IdAxe}' AND f.IdForm = fa.IdForm"
					;
				
				$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
				while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
				{
					echo "<h5 align=\"center\">$oEnreg->Titre</h5>";
				}
				$this->oBdd->libererResult($hResultInt);
			}
		}
		else 	//Effacement sans vérification
		{
			$sRequeteSql = "DELETE FROM Axe WHERE IdAxe ='{$this->oEnregBdd->IdAxe}'";
			//echo "<br>effacer Axe() : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		return TRUE;
	}
	
	function verificationdependances($v_bEnTete= FALSE)
	{
		$sRequeteSql = "Select * FROM Formulaire_Axe WHERE IdAxe ='{$this->oEnregBdd->IdAxe}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$iNbFormUtiliseAxe = $this->oBdd->retNbEnregsDsResult();
		
		if ($iNbFormUtiliseAxe > 0)
		{
			//On recherche et affiche le nom du/des formulaire(s) où l'axe est utilisé
			$sRequeteSql =
				"  SELECT f.Titre, fa.IdAxe"
				." FROM Formulaire as f, Formulaire_Axe as fa"
				." WHERE fa.IdAxe = '{$this->oEnregBdd->IdAxe}' AND f.IdForm = fa.IdForm";
			
			if ($v_bEnTete == TRUE)
				{echo "<h5 align=\"center\">La modification intervient dans : </h5>";}
			$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
			{
				echo "<h5 align=\"center\">$oEnreg->Titre</h5>";
			}
			$this->oBdd->libererResult($hResultInt);
		}
		return $iNbFormUtiliseAxe;
	}
	
	function enregistrer()
	{
		$sRequeteSql =
			($this->retId() > 0 ? "UPDATE Axe SET" : "INSERT INTO Axe SET")
			." IdAxe='{$this->oEnregBdd->IdAxe}'"
			." , DescAxe='{$this->oEnregBdd->DescAxe}'"
			.($this->oEnregBdd->IdAxe > 0 ? " WHERE IdAxe='{$this->oEnregBdd->IdAxe}'" : NULL);
		//echo "<br>enregistrer Axe: ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		return TRUE;
	}
	
	//Fonctions de définitions
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdObjForm = $v_iIdAxe; }
	function defDescAxe($v_sDescAxe) { $this->oEnregBdd->DescAxe = $v_sDescAxe; }
	
	// Fonctions de retour
	function retId() { return $this->oEnregBdd->IdAxe; }
	function retDescAxe() { return $this->oEnregBdd->DescAxe; }
	function retDesc() { return $this->retDescAxe(); }
	function retNom() { return $this->retDescAxe(); }
}

?>
