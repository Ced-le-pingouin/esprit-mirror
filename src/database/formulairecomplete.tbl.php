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
 * @file	formulairecomplete.tbl.php
 * 
 * Contient la classe de gestion des formulaires complétés, en rapport avec la DB
 * 
 * @date	2004/11/09
 * 
 * @author	Ludovic FLAMME
 */

require_once(dir_database("ressource.def.php"));

/**
* Gestion des formulaires complétés, et encapsulation de la table FormulaireComplete de la DB
*/
class CFormulaireComplete
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id du module à récupérer dans la DB
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $oAuteur;				///< Variable de type CPersonne, contenant la personne qui a complété le formulaire
	var $oFormulaireModele;
	var $asTablesReponses = array("ReponseCar", "ReponseTexte", "ReponseFlottant", "ReponseEntier");
	var $asChampsReponses = array("Valeur", "Valeur", "Valeur", "IdPropRep");
	var $abVerifZeroReponses = array(FALSE, FALSE, FALSE, TRUE);
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CFormulaireComplete(&$v_oBdd,$v_iId=0)
	{
		$this->oBdd = &$v_oBdd;
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
			$this->iId = $this->oEnregBdd->IdFC;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM FormulaireComplete WHERE IdFC='{$this->iId}' LIMIT 1";
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
		  && $v_oFormulaireModele->retId() > 0 && $v_oFormulaireModele->retId() == $this->retIdFormul())
		{
			$this->oFormulaireModele = $v_oFormulaireModele;
		}
		else if ($this->retIdFormul() > 0)
		{
			$this->oFormulaireModele = new CFormulaire($this->oBdd, $this->retIdFormul());
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
				" SELECT *"
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
	function ajouter($v_iIdPers,$v_iIdForm)
	{
		$sRequeteSql = "INSERT INTO FormulaireComplete SET"
					." IdPers='{$v_iIdPers}'"
					.", DateFC=NOW()"
					.", IdFormul='{$v_iIdForm}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		$this->defIdPers($v_iIdPers);
		return ($this->iId);
	}
	
	function retNbreFormulaireComplete($v_iIdSousActiv,$v_iIdPers)
	{
		$sRequeteSql = "  SELECT COUNT(*)"
					." FROM FormulaireComplete_SousActiv AS fcsa, FormulaireComplete AS fc"
					." WHERE fcsa.IdSousActiv='{$v_iIdSousActiv}' AND fcsa.IdFC=fc.IdFC AND fc.IdPers='{$v_iIdPers}'";
		$hResult = 	$this->oBdd->executerRequete($sRequeteSql);
		$iNbreFC = $this->oBdd->retEnregPrecis($hResult, 0);
		return $iNbreFC;
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
		
		$iNumVersion = $this->retNbreFormulaireComplete($v_iIdSousActiv,$this->retIdPers()) + 1;
		
		$this->oBdd->executerRequete("UPDATE FormulaireComplete SET TitreFC='{$iNumVersion}' WHERE IdFC='".$this->retId()."'");
		
		$sRequeteSql = "INSERT INTO FormulaireComplete_SousActiv SET"
			." IdFC='".$this->retId()."'"
			.", IdSousActiv='{$v_iIdSousActiv}'"
			.", StatutFormSousActiv='{$v_iStatutFormulaire}'";
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
	function enregistrer()
	{
		$sRequeteSql = "INSERT INTO FormulaireComplete SET"
			." IdPers='{$this->oEnregBdd->IdPers}'"
			.", DateFC='{$this->oEnregBdd->DateFC}'"
			.", IdFormul='{$this->oEnregBdd->IdFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface le formulaire complété courant ainsi que les réponses correspondantes 
	 */
	function effacer()
	{
		$sRequeteSql = "DELETE FROM ReponseEntier WHERE IdFC='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseTexte WHERE IdFC='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseCar WHERE IdFC='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM ReponseFlottant WHERE IdFC='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM FormulaireComplete WHERE IdFC='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function initAuteur()
	{
		$this->oAuteur = new CPersonne($this->oBdd,$this->retIdPers());
	}
	
	function retTexteStatut($v_iStatut=NULL)
	{
		if (empty($v_iStatut))
			$v_iStatut = $this->oEnregBdd->StatutFormSousActiv;
		
		switch ($v_iStatut)
		{
			case STATUT_RES_EN_COURS: return "en cours";
			case STATUT_RES_SOUMISE: return "soumis";
			case STATUT_RES_APPROF: return "à approfondir";
			case STATUT_RES_ACCEPTEE: return "accepter";
			case STATUT_RES_AUTOCORRIGEE: return "commentaire";
		}
	}
	
	/**
	 *  Verrouille les tables en relation avec la table FormulaireComplete
	 */
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
			
			." , TypeObjetFormul READ"
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
	/**
	 * Déverrouille les tables verrouillées par la fonction verrouillerTables()
	 */
	function deverrouillerTables()
	{
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	/**
	 * retourne une liste des formulaires complétés d'un formulaire (activité en ligne)
	 * 
	 * @param	v_iIdFormul l'id du formulaire de base
	 * 
	 * @return	une liste des formulaires complétés d'un formulaire (activité en ligne)
	 */
	function retListeFormulaireComplete($v_iIdFormul)
	{
		$aoListeFC = array();
		$sRequeteSql = "SELECT FC.*, P.Nom, P.Prenom "
					."FROM FormulaireComplete AS FC "
					."INNER JOIN Personne AS P ON FC.IdPers = P.IdPers "
					."INNER JOIN ("
						."SELECT MAX( DateFC ) AS Date,IdPers,IdFormul "
						."FROM FormulaireComplete "
						."WHERE IdFormul='$v_iIdFormul' "
						."GROUP BY IdPers "
						.") AS MaxDate "
					."ON MaxDate.Date=FC.DateFc AND MaxDate.IdPers=FC.IdPers AND MaxDate.IdFormul=FC.IdFormul";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$aoListeFC[] = $oEnreg;
		$this->oBdd->libererResult($hResult);
		return $aoListeFC;
	}
	
	/** @name Fonctions de définition des champs pour ce formulaire (activité en ligne) complété */
	//@{
	function defId ($v_iIdFC) { $this->iId = $v_iIdFC; }
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defDate ($v_sDateFC) { $this->oEnregBdd->DateFC = $v_sDateFC; }
	function defIdFormul ($v_iIdFormul) { $this->oEnregBdd->IdFormul = $v_iIdFormul; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce formulaire (activité en ligne) complété */
	//@{
	function retId () { return $this->iId; }
	function retTitre () { return $this->oEnregBdd->TitreFC; }
	function retIdParent () { return $this->oEnregBdd->IdSousActiv; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	function retDate () { return retDateFormatter($this->oEnregBdd->DateFC); }
	function retIdFormul () { return $this->oEnregBdd->IdFormul; }
	function retStatut () { return (isset($this->oEnregBdd->StatutFormSousActiv) ? $this->oEnregBdd->StatutFormSousActiv : STATUT_RES_EN_COURS); }
	//@}
}


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
				." WHERE IdFCSousActiv='{$this->iIdFCSA}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdFC;
	}
	
	function initParFcEtSsActiv($v_iIdFc,$v_iIdSousActiv)
	{
		$sRequeteSql = "SELECT FormulaireComplete.*"
				.", FormulaireComplete_SousActiv.IdFCSousActiv"
				.", FormulaireComplete_SousActiv.StatutFormSousActiv"
				.", FormulaireComplete_SousActiv.IdSousActiv"
				." FROM FormulaireComplete_SousActiv"
				." LEFT JOIN FormulaireComplete ON FormulaireComplete.IdFC=FormulaireComplete_SousActiv.IdFC"
				." WHERE FormulaireComplete.IdFC='$v_iIdFc' AND IdSousActiv='$v_iIdSousActiv'"
				." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		$this->iId = $this->oEnregBdd->IdFC;
	}
	
	
	// {{{ Méthodes de retour
	function retIdFCSA() { return (isset($this->oEnregBdd->IdFCSousActiv) ? $this->oEnregBdd->IdFCSousActiv : 0); }
	//function retIdFC () { return (isset($this->oEnregBdd->IdFC) ? $this->oEnregBdd->IdFC : 0); }
	//function retIdSousActiv () { return ; }
	// }}}
}

?>
