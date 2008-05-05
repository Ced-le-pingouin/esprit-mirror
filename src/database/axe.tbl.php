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

/**
 * @file	axe.tbl.php
 * 
 * Contient la classe de gestion des axes, en rapport avec la DB
 * 
 * @date	2004/05/05
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des axes, et encapsulation de la table Axe de la DB
*/
class CAxe 
{
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB
	
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CAxe(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
						  //si 0 crée un objet presque vide sinon 
						  //rempli l'objet avec les données de la table Axe
						  //de l'elément ayant l'Id passé en argument
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = " SELECT * FROM Axe"
						." WHERE IdAxe='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdAxe;
	}

	/**
	 * Ajoute un nouvel axe
	 * 
	 * @return	l'id du nouvel axe
	 */
	function ajouter()
	{
		$sRequeteSql = "INSERT INTO Axe SET IdAxe=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	/**
	 * Efface l'axe courant
	 * 
	 * @param	v_bVerification si \c true, verification que le l'axe n'est plus utilisé par des formulaires
	 * 
	 * @return	un tableau contenant en premier une valeur booléenne qui est à \c true si l'axe a été effacé et en
	 * 			deuxième paramètre le texte à afficher
	 */
	function effacer($v_bVerification = TRUE)
	{
		$amSortie = array();
		if ($v_bVerification)
		{
			//Avant d'effacer on vérifie que l'axe n'est utilisé par aucun formulaire
			$sRequeteSql = "SELECT COUNT(*) FROM Formulaire_Axe WHERE IdAxe ='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNbFormUtiliseAxe = $this->oBdd->retEnregPrecis($hResult);
			$this->oBdd->libererResult($hResult);
			
			if ($iNbFormUtiliseAxe == 0)
			{
				$sRequeteSql = "DELETE FROM Axe WHERE IdAxe ='{$this->iId}'";
				$this->oBdd->executerRequete($sRequeteSql);
				$amSortie[0] = TRUE;
				$amSortie[1] = "L'axe \"{$this->oEnregBdd->DescAxe}\" a été supprimé";
			}
			else
			{
				$amSortie[0] = FALSE;
				$amSortie[1] = "Suppression impossible car cet axe est utilisé dans :<ul>";
				
				//On recherche et affiche le nom du/des formulaire(s) où l'axe est utilisé
				$sRequeteSql = "SELECT f.Titre, fa.IdAxe"
						." FROM Formulaire as f, Formulaire_Axe as fa"
						." WHERE fa.IdAxe = '{$this->oEnregBdd->IdAxe}' AND f.IdFormul = fa.IdFormul";
				
				$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
				while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
				{
					$amSortie[1].= "<li>$oEnreg->Titre</li>";
				}
				$amSortie[1].="</ul>";
				$this->oBdd->libererResult($hResultInt);
			}
		}
		else 	//Effacement sans vérification
		{
			$sRequeteSql = "DELETE FROM Axe WHERE IdAxe ='{$this->iId}'";
			$this->oBdd->executerRequete($sRequeteSql);
			$amSortie[0] = TRUE;
			$amSortie[1] = "L'axe \"{$this->oEnregBdd->DescAxe}\" a été supprimé";
		}
		return $amSortie;
	}
	
	/**
	 * Verifie si l'axe courant est utilisé dans des formulaires
	 * 
	 * @return	la liste à afficher des dépendances
	 */
	function verificationdependances($v_bEnTete= FALSE)
	{
		$sSortie = "";
		$sRequeteSql = "SELECT COUNT(*) FROM Formulaire_Axe WHERE IdAxe ='{$this->iId}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbFormUtiliseAxe = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		if ($iNbFormUtiliseAxe > 0)
		{
			//On recherche et affiche le nom du/des formulaire(s) où l'axe est utilisé
			$sRequeteSql = "SELECT f.Titre, fa.IdAxe"
						." FROM Formulaire as f, Formulaire_Axe as fa"
						." WHERE fa.IdAxe = '{$this->iId}' AND f.IdFormul = fa.IdFormul";
			
			$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
			$sSortie = "Liste des dépendances: <ul>";
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
			{
				$sSortie .= "<li>$oEnreg->Titre</li>";
			}
			$this->oBdd->libererResult($hResultInt);
			$sSortie .= "</ul>";
		}
		return $sSortie;
	}
	
	/**
	 * Met à jour l'axe courant
	 */
	function enregistrer()
	{
		$sRequeteSql =
			($this->retId() > 0 ? "UPDATE Axe SET" : "INSERT INTO Axe SET")
			." IdAxe='{$this->oEnregBdd->IdAxe}'"
			." , DescAxe='{$this->oEnregBdd->DescAxe}'"
			.($this->oEnregBdd->IdAxe > 0 ? " WHERE IdAxe='{$this->oEnregBdd->IdAxe}'" : NULL);
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne un tableau contenant la liste des axes
	 * 
	 * @param	bTriAlpha	si \c true, le tableau sera trié alphabétiquement sur le champ DescAxe
	 * 
	 * @return	un tableau contenant la liste des axes
	 */
	function retListeAxes($bTriAlpha=TRUE)
	{
		$aoAxes = array();
		
		$sRequeteSql = "SELECT * FROM Axe ".($bTriAlpha ? "order by DescAxe" : "order by IdAxe");
		$hResult = $this->oBdd->executerRequete($sRequeteSql);

		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$aoAxes[] = $oEnreg;
		}
		$this->oBdd->libererResult($hResult);
		
		return $aoAxes;
	}
	
	/** @name Fonctions de définition des champs pour cet axe */
	//@{
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdAxe = $v_iIdAxe; }
	function defDescAxe($v_sDescAxe) { $this->oEnregBdd->DescAxe = $v_sDescAxe; }
	//@}

	/** @name Fonctions de lecture des champs pour cet axe */
	//@{
	function retId() { return $this->oEnregBdd->IdAxe; }
	function retDescAxe() { return $this->oEnregBdd->DescAxe; }
	function retDesc() { return $this->retDescAxe(); }
	function retNom() { return $this->retDescAxe(); }
	//@}
}
?>
