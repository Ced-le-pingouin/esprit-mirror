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
 * @file	formulaire_axe.tbl.php
 * 
 * Contient la classe de gestion des axes des formulaires, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des axes des formulaires, et encapsulation de la table Formulaire_Axe de la DB
*/
class CFormulaire_Axe
{
	var $oBdd;
	var $oEnregBdd;
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CFormulaire_Axe(&$v_oBdd,$v_iIdFormulaire=0,$v_iIdAxe=0) 
	{
		$this->oBdd = &$v_oBdd;  
		$this->iIdFormul = $v_iIdFormulaire;
		$this->iIdAxe = $v_iIdAxe;
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
			$sRequeteSql = "SELECT * FROM Formulaire_Axe"
						." WHERE IdFormul='{$this->iIdFormul}'"
						." AND IdAxe='{$this->iIdAxe}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdFormul;  //Attention clé multiple -> incomplète
	}
	
	/**
	 * Ajoute une nouvelle relation entre un axe et un formulaire
	 */
	function ajouter()
	{
		$sRequeteSql = "INSERT INTO Formulaire_Axe SET IdFormul='{$this->iIdFormul}', IdAxe='{$this->iIdAxe}';";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/*
	** Fonction 		: AxesDsFormulaire
	** Description		: renvoie les  IdAxe des axes présent dans le formulaire dont on lui a envoyé l'Id
	** Entrée			: 
	**					$v_iIdForm	: numéro du formulaire à traiter
	** Sortie			:
	**					$TabAxesForm[] :un tableau contenant les Id des axes utilisés pour ce formulaire
	*/
	function AxesDsFormulaire($v_iIdFormulaire)
	{
		$sRequeteSql = "SELECT IdAxe FROM Formulaire_Axe WHERE IdFormul = '$v_iIdFormulaire'";
		$hResultInt = $this->oBdd->executerRequete($sRequeteSql);
		$i = 0;
		$TabAxesForm = array();
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
		{
			$TabAxesForm[$i] = $oEnreg->IdAxe;
			$i++;
		}
		$this->oBdd->libererResult($hResultInt);
		return $TabAxesForm;
	}

	/*
	** Fonction 		: NomsFormulaires
	** Description		: renvoie le/les nom(s) de(s) formulaire(s) dans le ou lesquels l'axe, dont on a recu l'Id, est présent 
	** Entrée			: 
	**					$v_iIdAxe	: numéro de l'axe à traiter
	** Sortie			:
	**					$TabNomsForm[] :un tableau contenant les noms des formulaires qui utilisent cet axe
	*/
	function NomsFormulaires($v_iIdAxe)
	{
		$sRequeteSql = "SELECT f.Titre, fa.IdAxe"
					." FROM Formulaire as f, Formulaire_Axe as fa"
					." WHERE fa.IdAxe = '$v_iIdAxe' AND f.IdFormul = fa.IdFormul";
		
		$hResultInt = $this->oBdd->executerRequete($sRequeteSql);
		$i=0;
		$TabNomsForm[]=array();
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
		{
			$TabNomsForm[$i] = $oEnreg->Titre;
			$i++;
		}
		
		$this->oBdd->libererResult($hResultInt);
		return $TabNomsForm;
	}
	
	/*
	** Fonction 		: effacerAxesForm
	** Description		: efface tous les axes d'un formulaire donné
	** Entrée			: 
	**					$v_iIdFormulaire	: numéro du formulaire à traiter
	** Sortie			:
	*/
	function effacerAxesForm($v_iIdFormulaire)
	{
		$sRequeteSql = "DELETE FROM Formulaire_Axe"
					." WHERE IdFormul ='{$v_iIdFormulaire}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function copier($v_iIdNvForm)
	{
		if ($v_iIdNvForm < 1)
			return;
		
		$sRequeteSql = "INSERT INTO Formulaire_Axe SET"									  
					." IdFormul='{$v_iIdNvForm}'"
					.", IdAxe='{$this->oEnregBdd->IdAxe}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		$v_iIdNvForm = $this->oBdd->retDernierId();
		
		return $v_iIdNvForm;
	}
	
	/** @name Fonctions de définition des champs pour cette relation axe/formulaire */
	//@{
	function defIdFormul($v_iIdFormulaire) { $this->oEnregBdd->IdFormul = $v_iIdFormulaire; }
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdAxe = $v_iIdAxe; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cette relation axe/formulaire */
	//@{
	function retIdFormul() { return $this->oEnregBdd->IdFormul; }
	function retIdAxe() { return $this->oEnregBdd->IdAxe; }
	//@}
}
?>
