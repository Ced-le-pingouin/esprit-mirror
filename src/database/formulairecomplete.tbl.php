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
** Fichier ................: formulairecomplete.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 09/11/2004
** Auteurs ................: Ludovic FLAMME
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ressource.def.php"));

class CFormulaireComplete
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $oFormulaireModele;
	var $asTablesReponses = array("ReponseCar", "ReponseTexte", "ReponseFlottant", "ReponseEntier");
	var $asChampsReponses = array("Valeur", "Valeur", "Valeur", "IdReponse");
	var $abVerifZeroReponses = array(FALSE, FALSE, FALSE, TRUE);
	
 	/*
	** Fonction 	: CFormulaireComplete
	** Description	: constructeur
	** Entrée		: 
	**	 		&$v_oBdd : référence de l'objet Bdd appartenant a l'objet Projet
	**			$v_iId : identifiant d'un objet "formulaire complete"
	** Sortie		: 
	*/
	function CFormulaireComplete (&$v_oBdd,$v_iId=0)
	{
		$this->oBdd = &$v_oBdd;
		
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
 	/*
	** Fonction 	: init
	** Description	: permet d'initialiser l'objet QCocher soit en lui passant
	**					  un enregistrement provenant de la BD, soit en effectuant 
	**					  directement une requête dans la BD avec 
	**                	  l'id passé via la constructeur
	** Entrée		:
	**			$v_oEnregExistant=NULL : enregistrement représentant une question 
	**			de type "case à cocher"
	** Sortie		: 
	*/
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdFC;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM FormulaireComplete"
			." WHERE IdFC='{$this->iId}'"
			." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initFormulaireModele($v_oFormulaireModele = NULL)
	{
		if (isset($this->oFormulaireModele))
			return;
		
		if (isset($v_oFormulaireModele) && mb_strtolower(get_class($v_oFormulaireModele),"UTF-8") == "cformulaire"
		  && $v_oFormulaireModele->retId() > 0 && $v_oFormulaireModele->retId() == $this->retIdForm())
		{
			$this->oFormulaireModele = $v_oFormulaireModele;
		}
		else if ($this->retIdForm() > 0)
		{
			$this->oFormulaireModele = new CFormulaire($this->oBdd, $this->retIdForm());
		}
	}
	
	function initReponses()
	{
		if (!isset($this->oFormulaireModele))
			$this->initFormulaireModele();
		
		$this->oFormulaireModele->initAxes();
		$this->oFormulaireModele->initObjets(TRUE, TRUE);
		
		for ($i = 0; $i < count($this->asTablesReponses); $i++)
		{
			$hResult = $this->oBdd->executerRequete
			(
				"  SELECT *"
				." FROM ".$this->asTablesReponses[$i]
				." WHERE IdFC='".$this->retId()."'"
			);
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				if (isset($this->oFormulaireModele->aoObjets[$oEnreg->IdObjFormul]))
				{
					$sNomChamp = $this->asChampsReponses[$i];
					if (!$this->abVerifZeroReponses[$i] || $oEnreg->$sNomChamp > 0)
						$this->oFormulaireModele->aoObjets[$oEnreg->IdObjFormul]->sReponse[] = $oEnreg->$sNomChamp;
				}
			}
			
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/*
	** Fonction 	: ajouter
	** Description	: crée un enregistrement dans la table FormulaireComplete
	** Entrée		:
				$v_iIdPers : identifiant de la personne qui a soumis le formulaire
				$v_iIdForm : identifiant du formulaire de base
	** Sortie		: Id renvoyé par la BD
	*/
	function ajouter ($v_iIdPers,$v_iIdForm)
	{
		$sRequeteSql = "INSERT INTO FormulaireComplete SET"
			." IdPers='{$v_iIdPers}'"
			.", DateFC=NOW()"
			.", IdForm='{$v_iIdForm}'";
		//echo "<br>ajouter FC(): ".$sRequeteSql."<br>";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		$this->defIdPers($v_iIdPers);
		return ($this->iId);
	}
	
	function deposerDansSousActiv($v_iIdSousActiv, $v_iStatutFormulaire)
	{
		$this->oBdd->executerRequete
		(
			" LOCK TABLES"
			." FormulaireComplete AS fc READ"
			." , FormulaireComplete WRITE"
			." , FormulaireComplete_SousActiv AS fcsa READ"
			." , FormulaireComplete_SousActiv WRITE"
		);
		
		$hResult = 
			$this->oBdd->executerRequete
			(
				"  SELECT COUNT(*)"
				." FROM FormulaireComplete_SousActiv AS fcsa, FormulaireComplete AS fc"
				." WHERE fcsa.IdSousActiv='{$v_iIdSousActiv}' AND fcsa.IdFC=fc.IdFC AND fc.IdPers='".$this->retIdPers()."'"
			);
		
		$iNumVersion = $this->oBdd->retEnregPrecis($hResult, 0) + 1;
		
		$this->oBdd->executerRequete("UPDATE FormulaireComplete SET TitreFC='{$iNumVersion}' WHERE IdFC='".$this->retId()."'");
		
		$sRequeteSql = "INSERT INTO FormulaireComplete_SousActiv SET"
			." IdFC='".$this->retId()."'"
			.", IdSousActiv='{$v_iIdSousActiv}'"
			.", StatutFormSousActiv='{$v_iStatutFormulaire}'";
		//echo "<br>ajouter FC() à SousActiv: ".$sRequeteSql."<br>";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		/*$this->iId = $this->oBdd->retDernierId($hResult);
		return ($this->iId);*/
	}
	
	/*
	** Fonction 		: enregistrer
	** Description		: enregistre les données de l'objet courant dans la BD
	** Entrée			:
	** Sortie			:
	*/
	function enregistrer ()
	{
		$sRequeteSql = "INSERT INTO FormulaireComplete SET"
			." IdPers='{$this->oEnregBdd->IdPers}'"
			.", DateFC='{$this->oEnregBdd->DateFC}'"
			.", IdForm='{$this->oEnregBdd->IdForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return TRUE;
	}
	
	/*
	** Fonction 		: effacer
	** Description		: efface de la BD l'enregistrement concernant l'objet courant
						  et toutes les réponses correspondantes
	** Entrée			:
	** Sortie			:
	*/
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM ReponseEntier"
			." WHERE IdFC='{$this->oEnregBdd->IdFC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseTexte"
			." WHERE IdFC='{$this->oEnregBdd->IdFC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseCar"
			." WHERE IdFC='{$this->oEnregBdd->IdFC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseFlottant"
			." WHERE IdFC='{$this->oEnregBdd->IdFC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM FormulaireComplete"
			." WHERE IdFC='{$this->oEnregBdd->IdFC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function initAuteur () { $this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers); }
	
	function retTexteStatut ($v_iStatut=NULL)
	{
		if (empty($v_iStatut))
			$v_iStatut = $this->oEnregBdd->StatutFormSousActiv;
		
		switch ($v_iStatut)
		{
			case STATUT_RES_EN_COURS: return "en cours";
			case STATUT_RES_SOUMISE: return "soumis";
			case STATUT_RES_APPROF: return "à approfondir";
			case STATUT_RES_ACCEPTEE: return "accepter";
		}
	}
	
	function verrouillerTables()
	{
		$this->oBdd->executerRequete
		(
			"LOCK TABLES"
			." FormulaireComplete WRITE"
			
			." , ReponseEntier WRITE"
			." , ReponseFlottant WRITE"
			." , ReponseCar WRITE"
			." , ReponseTexte WRITE"
			
			." , FormulaireComplete_SousActiv WRITE"
			." , SousActiv READ"
			
			." , Formulaire WRITE"
			." , Axe READ"
			." , PropositionReponse READ"
			." , Formulaire_Axe READ"
			." , Reponse_Axe READ"
			
			." , TypeObjetForm READ"
			." , ObjetFormulaire READ"
			." , MPTexte READ"
			." , MPSeparateur READ"
			." , QNombre READ"
			." , QTexteCourt READ"
			." , QTexteLong READ"
			." , QRadio READ"
			." , QListeDeroul READ"
			." , QCocher READ"
		);
	}
	
	function deverrouillerTables()
	{
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}

 	// {{{ Méthodes de définition
	function defId ($v_iIdFC) { $this->iId = $v_iIdFC; }
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defDate ($v_sDateFC) { $this->oEnregBdd->DateFC = $v_sDateFC; }
	function defIdForm ($v_iIdForm) { $this->oEnregBdd->IdForm = $v_iIdForm; }
	// }}}
	
	// {{{ Méthodes de retour
	function retId () { return $this->iId; }
	function retTitre () { return $this->oEnregBdd->TitreFC; }
	function retIdParent () { return $this->oEnregBdd->IdSousActiv; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	function retDate () { return retDateFormatter($this->oEnregBdd->DateFC); }
	function retIdForm () { return $this->oEnregBdd->IdForm; }
	function retStatut () { return (isset($this->oEnregBdd->StatutFormSousActiv) ? $this->oEnregBdd->StatutFormSousActiv : STATUT_RES_EN_COURS); }
	// }}}
}


///////////////////////
class CFormulaireComplete_SousActiv extends CFormulaireComplete
{
	function CFormulaireComplete_SousActiv (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdFCSA = $v_iId;
		
		if (isset($this->iIdFCSA))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iIdFCSA = $this->oEnregBdd->IdFCSousActiv;
		}
		else
		{
			$sRequeteSql = "SELECT FormulaireComplete.*"
				.", FormulaireComplete_SousActiv.IdFCSousActiv"
				.", FormulaireComplete_SousActiv.StatutFormSousActiv"
				.", FormulaireComplete_SousActiv.IdSousActiv"
				." FROM FormulaireComplete_SousActiv"
				." LEFT JOIN FormulaireComplete ON FormulaireComplete.IdFC=FormulaireComplete_SousActiv.IdFC"
				." FROM IdFCSousActiv='{$this->iIdFCSA}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdFC;
	}
	
	/*function initFormulairesCompletes ($v_iIdSousActiv)
	{
	}*/
	
	// {{{ Méthodes de retour
	function retIdFCSA () { return (isset($this->iIdFCSA) ? $this->iIdFCSA : 0); }
	//function retIdFC () { return (isset($this->oEnregBdd->IdFC) ? $this->oEnregBdd->IdFC : 0); }
	//function retIdSousActiv () { return ; }
	// }}}
}

?>
