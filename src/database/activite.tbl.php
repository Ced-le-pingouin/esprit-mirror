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
 * @file	activite.tbl.php
 * 
 * Contient la classe de gestion des activités, en rapport avec la DB
 * 
 * @date	2001/06/01
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 */

require_once(dir_database("sous_activite.tbl.php"));
require_once(dir_database("equipe.tbl.php"));
require_once(dir_lib("std/FichierInfo.php", TRUE));

define("INTITULE_ACTIV","Groupe d'actions"); /// Titre qui désigne le quatrième niveau de la structure d'une formation 	@enum INTITULE_ACTIV

/**
 * Gestion des activités, et encapsulation de la table Activ de la DB
 */
class CActiv
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id de l'activité à récupérer dans la DB

	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici

	var $b_RemettreDeOrdre;		///< Valeur booléenne indiquant s'il faut réorganiser les numéros d'ordre des activitées
	var $oSousActivCourante;	///< Objet de type CSousActiv contenant une activité
	var $aoSousActivs;			///< Tableau rempli par #initSousActivs(), contenant une liste des sous-activités de cette activité
	var $oEquipe;				///< Objet de type CEquipe contenant une équipe
	var $aoEquipes;				///< Tableau rempli par #initEquipes(), contenant une liste des équipes de cette activité
	var $aoActivs;				///< Tableau rempli par #retListeActivs(), contenant une liste des activités de la rubrique
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CActiv (&$v_oBdd,$v_iIdActiv=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdActiv;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdActiv;
		}
		else
		{
			$sRequeteSql = "SELECT Module.IdForm"
				.", Module.IdMod"
				.", Activ.*"
				." FROM Activ"
				." LEFT JOIN Module_Rubrique USING (IdRubrique)"
				." LEFT JOIN Module USING (IdMod)"
				." WHERE Activ.IdActiv='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Permet de connaître le numero d'ordre maximum des activités
	 * 
	 * @return	le numéro d'ordre maximum
	 */
	function retNumOrdreMax ($v_iIdRubrique=NULL)
	{
		if ($v_iIdRubrique == NULL)
			$v_iIdRubrique = $this->oEnregBdd->IdRubrique;
		$sRequeteSql = "SELECT MAX(OrdreActiv) FROM Activ WHERE IdRubrique='".$v_iIdRubrique."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumOrdreMax = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNumOrdreMax;
	}
	
	/**
	 * Copie l'activité courante vers une rubrique spécifique
	 * 
	 * @param	v_iIdRubrique	l'id de la rubrique
	 * @param	v_bRecursive	si \c true, copie aussi les sous-activités associées à l'activité
	 * 
	 * @return	l'id de la nouvelle activité
	 */
	function copier($v_iIdRubrique, $v_bRecursive = TRUE, $v_sExportation = NULL)
	{
		global $oArchiveExport;
		
		$iIdActiv = $this->copierActivite($v_iIdRubrique, $v_sExportation);
		
		if ($iIdActiv < 1 && !$v_sExportation)
			return 0;
		
		if (!$v_sExportation)
		{
			// -------------------
			// Copier le répertoire de l'activité actuelle
			// vers la nouvelle activité
			// -------------------
			$oActiv = new CActiv($this->oBdd,$iIdActiv);
			
			$sRepSrc = dir_cours($this->retId(),$this->retIdFormation());
			$sRepDst = dir_cours($iIdActiv,$oActiv->retIdFormation());
			
			copyTree($sRepSrc, $sRepDst, $v_sExportation, &$oArchiveExport);
			
			// Vider les répertoires contenant les fichiers
			// des collecticiels (sauf le document de base) et des chats
			$oActiv->effacerRepDocuments(FALSE,"·*\-([0-9]{4})\..*");
			$oActiv->effacerRepChats(FALSE);
			
			unset($oActiv);
		}
		
		// -------------------
		// Copier les sous-activités
		// -------------------
		if ($v_bRecursive)
			$this->copierSousActivites($iIdActiv, $v_sExportation);
		
		return $iIdActiv;
	}
	
	/**
	 * Insère une copie d'une activité dans la DB
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique
	 * 
	 * @return	l'id de la nouvelle activité
	 */
	function copierActivite($v_iIdRubrique, $v_sExportation = NULL)
	{
		global $sSqlExportForm;
		
		if ($v_iIdRubrique < 1 && !$v_sExportation)
			return 0;
		
		$sRequeteSql = "INSERT INTO Activ SET"
			." IdActiv=".(!$v_sExportation?"NULL":"'".$this->retId()."'")
			.", NomActiv='".MySQLEscapeString($this->oEnregBdd->NomActiv)."'"
			.", DescrActiv='".MySQLEscapeString($this->oEnregBdd->DescrActiv)."'"
			.", DateDebActiv=NOW()"
			.", DateFinActiv=NOW()"
			.", StatutActiv='{$this->oEnregBdd->StatutActiv}'"
			.", AfficherStatutActiv='{$this->oEnregBdd->AfficherStatutActiv}'"
			.", ModaliteActiv='{$this->oEnregBdd->ModaliteActiv}'"
			.", AfficherModaliteActiv='{$this->oEnregBdd->AfficherModaliteActiv}'"
			.", InscrSpontEquipeA='0'"
			.", NbMaxDsEquipeA='0'"
			//.", IdRubrique=".(!$v_sExportation?"'{$v_iIdRubrique}'":"@iIdRubriqueCourante")
			.", IdRubrique=".(!$v_sExportation?"'{$v_iIdRubrique}'":"'".$this->retIdParent()."'")
			.", IdUnite='0'"
			.", OrdreActiv='{$this->oEnregBdd->OrdreActiv}'";
		
		if ($v_sExportation)
		{
			$sSqlExportForm .= $sRequeteSql . ";\n\n";
			$sSqlExportForm .= "SET @iIdActiviteCourante := LAST_INSERT_ID();\n\n";
			
			return -1;
		}
		else
		{
			$this->oBdd->executerRequete($sRequeteSql);
			
			return $this->oBdd->retDernierId();
		}
	}
	
	/**
	 * Copie les sous-activités de l'activité courante vers une autre
	 * 
	 * @param	v_iIdActiv l'id de l'activité de destination
	 */
	function copierSousActivites($v_iIdActiv, $v_sExportation = NULL)
	{
		$this->initSousActivs();
		foreach ($this->aoSousActivs as $oSousActiv)
			$oSousActiv->copier($v_iIdActiv, $v_sExportation);
		$this->aoSousActivs = NULL;
	}
	
	/**
	 * Réinitialise l'objet \c oEnregBdd avec l'activité courante
	 */
	function rafraichir ()
	{
		if ($this->retId() > 0)
			$this->init();
	}
	
	/**
	 * Retourne le chemin du répertoire qui abrite les fichiers de l'activité courante
	 * 
	 * @param	v_sFichierAInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourné sera absolu. Si \c false, il sera relatif
	 * 
	 * @return	le chemin vers le repertoire de l'activité courante
	 */
	function retRepCours ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
	{
		return dir_cours($this->iId,$this->oEnregBdd->IdForm,$v_sFichierAInclure,$v_bCheminAbsolu);
	}
	
	/**
	 * Retourne le nombre d'activités de cette rubrique
	 * 
	 * @param	v_iNumParent l'id de la rubrique
	 * 
	 * @return	le nombre d'activités de cette rubrique
	 */
	function retNombreLignes ($v_iNumParent=NULL)
	{
		if ($v_iNumParent == NULL)
			$v_iNumParent = $this->retIdParent();
		
		if ($v_iNumParent == NULL)
			return FALSE;
		
		$sRequeteSql = "SELECT COUNT(*) FROM Activ"
			." WHERE IdRubrique='{$v_iNumParent}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult,0);
		
		$this->oBdd->libererResult($hResult);
		
		return $iNbrLignes;
	}
	
	/**
	 * Ajoute une nouvelle activité à la rubrique
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique
	 * @param	v_iIdUnite l'id de "l'unité", n'est plus utilisé, champs toujours à 0
	 * 
	 * @return	L'id de la nouvelle activité
	 */
	function Ajouter ($v_iIdRubrique,$v_iIdUnite=0)
	{
		$iNumOrdre = $this->retNombreLignes($v_iIdRubrique)+1;
		
		$sRequeteSql = "INSERT INTO Activ SET"
			." IdActiv=NULL"
			.", NomActiv='".MySQLEscapeString(INTITULE_ACTIV." sans nom")."'"
			.", DateDebActiv=NOW()"
			.", DateFinActiv=NOW()"
			.", ModaliteActiv='".MODALITE_INDIVIDUEL."'"
			.", AfficherModaliteActiv='0'"
			.", StatutActiv='".STATUT_OUVERT."'"
			.", AfficherStatutActiv='0'"
			.", IdRubrique='{$v_iIdRubrique}'"
			.", IdUnite='{$v_iIdUnite}'"
			.", OrdreActiv='{$iNumOrdre}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	/**
	 * Définit la variable booléenne \c v_bRemettreDeOrdre
	 * 
	 * @param	v_bRemettreDeOrdre valeur à définir: \c true(défaut) ou \c false
	 */
	function defRemettreDeOrdre ($v_bRemettreDeOrdre=TRUE)
	{
		$this->b_RemettreDeOrdre = $v_bRemettreDeOrdre;
	}
	
	/**
	 * Retourne la valeur de la variable booléenne \c v_bRemettreDeOrdre
	 * 
	 * @return	la valeur de la variable booléenne \c v_bRemettreDeOrdre
	 */
	function retRemettreDeOrdre ()
	{
		return (is_bool($this->b_RemettreDeOrdre) ? $this->b_RemettreDeOrdre : TRUE);
	}
	
	
	/**
	 * Efface la totalité d'une activité (sous-activités comprises)
	 */
	function effacer ()
	{
		$this->effacerEquipes();
		
		// Rechercher toutes les sous-activités
		$this->initSousActivs();
		
		// Effacer toutes les sous-activités
		foreach ($this->aoSousActivs as $oSousActiv)
			$oSousActiv->effacer();
		
		if (PHP_OS === "Linux")
			exec("rm -rf ".dir_cours($this->retId(),$this->retIdFormation(),NULL,TRUE));
		
		// Effacer cette activité
		$sRequeteSql = "DELETE FROM Activ"
			." WHERE IdActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->retRemettreDeOrdre())
			$this->redistNumsOrdre();
		
		unset($this->iId,$this->oEnregBdd);
	}
	
	/**
	 * Efface les équipes de cette activité
	 */
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_ACTIVITE,$this->iId);
	}
	
	/**
	 * Efface les documents (collecticiel) et son répertoire(optionnel)
	 * 
	 * @param	v_bEffacerRepertoire	si \c true(défaut), efface le répertoire
	 * @param	sFiltreDocs				filtre pour conserver certains documents (filtre sur une partie du nom du fichier)
	 */
	function effacerRepDocuments ($v_bEffacerRepertoire=TRUE,$sFiltreDocs=NULL)
	{
		include_once(dir_lib("systeme_fichiers.lib.php",TRUE));
		$sRepDocs = dir_collecticiel($this->retIdFormation(),$this->retId(),NULL,TRUE);
		vider_repertoire($sRepDocs,$sFiltreDocs);
		if ($v_bEffacerRepertoire) @unlink($sRepDocs);
	}
	
	/**
	 * Efface le répertoire(optionnel) et toutes les archives des chats
	 * 
	 * @param	v_bEffacerRepertoire si \c true(défaut), efface le répertoire
	 */
	function effacerRepChats ($v_bEffacerRepertoire=TRUE)
	{
		include_once(dir_lib("systeme_fichiers.lib.php",TRUE));
		$sRepChats = dir_chat_log($this->retId(),$this->retIdFormation(),NULL,TRUE);
		vider_repertoire($sRepChats);
		if ($v_bEffacerRepertoire) @unlink($sRepChats);
	}
	
	
	/**
	 * Initialise la sous-activité courante
	 * 
	 * @param	v_iIdSousActiv l'id de la sous-activité
	 */
	function initSousActivCourante ($v_iIdSousActiv=NULL)
	{
		if ($v_iIdSousActiv>0)
		{
			$this->oSousActivCourante = new CSousActiv($this->oBdd, $v_iIdSousActiv);

			if ($this->oSousActivCourante->retIdParent() != $this->retId())
				unset($this->oSousActivCourante);
			else
				$this->oSousActivCourante->oActivParente = &$this;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM SousActiv"
				." WHERE IdActiv=".$this->retId()
				." AND OrdreSousActiv=1";

			$hResult = $this->oBdd->executerRequete($sRequeteSql);

			if ($this->oBdd->retNbEnregsDsResult($hResult))
			{
				$oEnreg = $this->oBdd->retEnregSuiv($hResult);

				$this->oSousActivCourante = new CSousActiv($this->oBdd);

				$this->oSousActivCourante->init($oEnreg);
				
				$this->oSousActivCourante->oActivParente = &$this;
			}
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Retourne le nombre d'équipes ratachées à cette activité
	 * 
	 * @return	le nombre d'équipes ratachées à cette activité
	 */
	function retNbrEquipes ()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Equipe"
			." WHERE Equipe.IdActiv='$this->iId'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iNbrEquipes = $this->oBdd->retEnregPrecis($hResult);
		
		$this->oBdd->libererResult($hResult);
		
		return $iNbrEquipes;
	}
	
	/**
	 * Initialise un tableau contenant une liste des sous-activités. Si l'id de la personne est fournie, elle l'initialise
	 * avec les sous-activités que cette personne peut voir
	 * 
	 * @param	v_iIdPers l'id de la personne(optionnel)
	 * 
	 * @return	le nombre de sous-activités insérés dans le tableau
	 */
	function initSousActivs ($v_iIdPers=NULL)
	{
		$iIndexSousActiv = 0;
		$this->aoSousActivs = array();
		
		if (isset($v_iIdPers))
			$sRequeteSql = "SELECT SousActiv.*"
				." FROM SousActiv"
				." LEFT JOIN SousActivInvisible"
					." ON SousActiv.IdSousActiv=SousActivInvisible.IdSousActiv"
					." AND SousActivInvisible.IdPers='{$v_iIdPers}'"
				." WHERE SousActiv.IdActiv='".$this->retId()."'"
					." AND SousActivInvisible.IdPers IS NULL"
				." ORDER BY SousActiv.OrdreSousActiv ASC";
		else
			$sRequeteSql = "SELECT * FROM SousActiv"
				." WHERE IdActiv='".$this->retId()."'"
				." ORDER BY OrdreSousActiv ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoSousActivs[$iIndexSousActiv] = new CSousActiv($this->oBdd);
			$this->aoSousActivs[$iIndexSousActiv]->init($oEnreg);
			$this->aoSousActivs[$iIndexSousActiv]->oActivParente = &$this;
			
			$iIndexSousActiv++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexSousActiv;
	}
	
	/**
	 * Retourne en français la modalité(constante MODALITE_) de l'activité 
	 * 
	 * @return	la modalité de l'activité
	 */
	function retTexteModalite ()
	{
		switch ($this->retModalite())
		{
			case MODALITE_INDIVIDUEL:
				$r_sTexteModalite = "individuel";
				break;

			case MODALITE_PAR_EQUIPE:
				$r_sTexteModalite = "par équipe";
				break;

			default:
				$r_sTexteModalite = "[MODALITE INCONNUE]";
		}
		
		return $r_sTexteModalite;
	}
	
	/**
	 * Retourne le statut(constante STATUT_) de l'activité
	 * 
	 * @return	le statut de l'activité
	 */
	function retTexteStatut ()
	{
		switch ($this->retStatut())
		{
			case STATUT_FERME: $r_sTexteStatut = "fermé"; break;
			case STATUT_OUVERT: $r_sTexteStatut = "ouvert"; break;
			case STATUT_ARCHIVE: $r_sTexteStatut = "archivé"; break;
			default: $r_sTexteStatut = "[STATUT INCONNU]";
		}
		return $r_sTexteStatut;
	}
	
	/**
	 * Initialise \c oEquipe avec l'équipe associée au niveau(activité->formation) auquel la personne, donnée en 
	 * paramètre, appartient
	 * 
	 * @param	v_iIdMembre		l'id de la personne
	 * @param	v_bInitMembres	si \c true, initialise les membres de l'équipe(\c false par défaut)
	 */
	function initEquipe ($v_iIdMembre,$v_bInitMembres=FALSE)
	{
		$this->oEquipe = NULL;
		
		$aiIds = $this->retTableauIdsParents();
		
		$sRequeteSql = "SELECT Equipe.* FROM Equipe"
			." LEFT JOIN Equipe_Membre USING (IdEquipe)"
			." WHERE Equipe_Membre.IdPers='{$v_iIdMembre}' AND ("
			." Equipe.IdActiv='".$aiIds[TYPE_ACTIVITE]."'"
			." OR Equipe.IdRubrique='".$aiIds[TYPE_RUBRIQUE]."'"
			." OR Equipe.IdMod='".$aiIds[TYPE_MODULE]."'"
			." OR Equipe.IdForm='".$aiIds[TYPE_FORMATION]."'"
			.")";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$bEstDansEquipe = TRUE;
			
			// Vérifier que l'étudiant fait vraiment parti d'un groupe
			if (($oEnreg->IdActiv > 0 && $aiIds[TYPE_ACTIVITE] != $oEnreg->IdActiv) ||
				($oEnreg->IdRubrique > 0 && $aiIds[TYPE_RUBRIQUE] != $oEnreg->IdRubrique) ||
				($oEnreg->IdMod > 0 && $aiIds[TYPE_MODULE] != $oEnreg->IdMod) ||
				($oEnreg->IdForm > 0 && $aiIds[TYPE_FORMATION] != $oEnreg->IdForm))
				$bEstDansEquipe = FALSE;
			
			if ($bEstDansEquipe)
			{
				$this->oEquipe = new CEquipe($this->oBdd);
				$this->oEquipe->init($oEnreg);
				
				if ($v_bInitMembres)
					$this->oEquipe->initMembres();
				
				// On peut quitter cette boucle
				break;
			}
		}
		
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Initialise un tableau contenant les équipes de l'activité
	 * 
	 * @param	v_bInitMembres			si \c true, initialise également les membres de l'équipe (défaut à \c false)
	 * @param	v_iTypeNiveauDepart		le numéro représentant le type d'élément (formation/module/etc) par lequel on veut 
	 * 									débuter la recherche des équipes à initialiser
	 * 
	 * @return	le nombre d'équipes insérés dans le tableau
	 */
	function initEquipes ($v_bInitMembres=FALSE,$v_iTypeNiveauDepart=TYPE_ACTIVITE)
	{
		$oListeEquipes = new CEquipe($this->oBdd);
		$oListeEquipes->initEquipesEx($this->retId(),$v_iTypeNiveauDepart,$v_bInitMembres);
		$this->aoEquipes = $oListeEquipes->aoEquipes;
		return count($this->aoEquipes);
	}
	
	/** @name Fonctions de lecture des champs pour cette activité */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retDateDeb () { return $this->oEnregBdd->DateDebActiv; }
	function retDateFin () { return $this->oEnregBdd->DateFinActiv; }
	
	function retModalite () { return $this->oEnregBdd->ModaliteActiv; }
	function retIdFormation ()
	{
		if (!isset($this->oEnregBdd->IdForm))
			$this->init();
		return $this->oEnregBdd->IdForm;
	}
	
	function retIdModule ()
	{
		if (!isset($this->oEnregBdd->IdMod))
			$this->init();
		return $this->oEnregBdd->IdMod;
	}
	
	function retIdRubrique ()
	{
		return $this->oEnregBdd->IdRubrique ;
	}
	
	function retTableauIdsParents ()
	{
		return array(NULL,$this->oEnregBdd->IdForm,$this->oEnregBdd->IdMod,$this->oEnregBdd->IdRubrique,NULL,$this->oEnregBdd->IdActiv,NULL);
	}

	function retStatut ()
	{
		return $this->oEnregBdd->StatutActiv;
	}
	
	function retInscrSpontEquipe () { return $this->oEnregBdd->InscrSpontEquipeA; }
	
	function retNbMaxDsEquipe () { return $this->oEnregBdd->NbMaxDsEquipeA; }
	
	function retIdParent ()
	{
		return $this->oEnregBdd->IdRubrique;
	}

	function retNumOrdre ()
	{
		return $this->oEnregBdd->OrdreActiv;
	}

	function retAfficherModalite ()
	{
		return $this->oEnregBdd->AfficherModaliteActiv;
	}

	function retAfficherStatut ()
	{
		return $this->oEnregBdd->AfficherStatutActiv;
	}
	
	function retNom ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->NomActiv) : $this->oEnregBdd->NomActiv);
	}
	
	function retDescr ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->DescrActiv) : $this->oEnregBdd->DescrActiv);
	}
	//@}

	/** @name Fonctions de définition des champs pour cette activité */
	//@{
	function defModalite ($v_iModalite) { $this->mettre_a_jour("ModaliteActiv",$v_iModalite); }

	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutActiv",$v_iStatut);
	}

		function defNumOrdre ($v_iOrdre)
	{
		if (is_numeric($v_iOrdre))
			$this->mettre_a_jour("OrdreActiv",$v_iOrdre);
	}

	function defAfficherModalite ($v_bAfficher)
	{
		if (is_bool($v_bAfficher))
			$this->mettre_a_jour("AfficherModaliteActiv",$v_bAfficher);
	}
	
	function defAfficherStatut ($v_bAfficher)
	{
		if (is_bool($v_bAfficher))
			$this->mettre_a_jour("AfficherStatutActiv",$v_bAfficher);
	}

	function defNom ($v_sNomActiv)
	{
		$v_sNomActiv = MySQLEscapeString($v_sNomActiv);
		
		if (empty($v_sNomActiv))
			$v_sNomActiv = INTITULE_ACTIV." sans nom";
		
		$this->mettre_a_jour("NomActiv",$v_sNomActiv);
	}
	
	function defDescr ($v_sDescrActiv)
	{
		$this->mettre_a_jour("DescrActiv",$v_sDescrActiv);
	}
	//@}
	
	/**
	 * Met à jour un champ de la table Activ
	 * 
	 * @param	v_sNomChamp		le nom du champ à mettre à jour
	 * @param	v_mValeurChamp	la nouvelle valeur du champ
	 * @param	v_iIdActiv		l'id de l'activité
	 * 
	 * @return	\c true si il a mis à jour le champ dans la DB
	 */
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdActiv=0)
	{
		if ($v_iIdActiv < 1)
			$v_iIdActiv = $this->retId();

		if ($v_iIdActiv < 1)
			return FALSE;

		$sRequeteSql = "UPDATE Activ SET"
			." {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'"
			." WHERE IdActiv='{$v_iIdActiv}'";

		$this->oBdd->executerRequete ($sRequeteSql);
		
		return TRUE;
	}

	/**
	 * Retourne la constante qui définit le niveau "activité", de la structure d'une formation
	 * 
	 * @return	la constante qui définit le niveau "activité", de la structure d'une formation
	 */
	function retTypeNiveau () { return TYPE_ACTIVITE; }
	
	/**
	 * Initialise un tableau avec la liste des activités de la rubrique
	 * 
	 * @return	le nombre d'activités insérées dans le tableau
	 */
	function retListeActivs ()
	{
		$iIdParent = $this->retIdParent();

		if (!isset($iIdParent))
			return 0;

		$sRequeteSql = "SELECT * FROM Activ"
			." WHERE IdRubrique='{$iIdParent}'"
			." ORDER BY OrdreActiv ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);

		$i = 0;

		while ($this->aoActivs[$i++] = $this->oBdd->retEnregSuiv($hResult))
			;

		return ($i-1);
	}

	/**
	 * Retourne un nombre representant le type de transfert entre l'activité courante et une activité dont l'id est 
	 * passé en paramètre. Les transferts se font uniquement entre activités "collecticiels" et les 2 activités doivent
	 * se situer dans la même rubrique. Les types de transfert se différencient par les combinaisons possibles des 
	 * modalités des collecticiels(individuel ou par équipe)
	 * 
	 * @param	v_iIdActivDst l'id de l'activité
	 * 
	 * @return	le type de transfert du collecticiel
	 */
	function retTypeTransfert ($v_iIdActivDst)
	{
		$iTransfert_II = 5;		// individuel -> individuel
		$iTransfert_IE = 9;		// individuel -> équipe
		$iTransfert_EE = 10;	// équipe -> équipe
		$iTransfert_EI = 6;		// équipe -> individuel
		
		$oActiv = new CActiv($this->oBdd,$v_iIdActivDst);
		
		$iTypeTransfert = ($this->retModalite() == MODALITE_INDIVIDUEL ? 1 : 2) + 
			($oActiv->retModalite() == MODALITE_INDIVIDUEL ? 4 : 8);
		
		switch ($iTypeTransfert)
		{
			case $iTransfert_II: return 1; break;
			case $iTransfert_IE: return 2; break;
			case $iTransfert_EE: return 3; break;
			case $iTransfert_EI: return 4; break;
		}
		
		return 0;	
	}

	/**
	 * Redistribue les numéros d'ordre des activités
	 * 
	 * @param	v_iNouveauNumOrdre	le nouveau numéro d'ordre de l'activité courante
	 * 
	 * @return	\c true si les numéros ont bien été modifiés
	 */
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if ($v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;

		if (($cpt = $this->retListeActivs()) < 0)
			return FALSE;

		// *************************************
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		// *************************************

		$aoNumsOrdre = array();

		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array ($this->aoActivs[$i]->IdActiv,$this->aoActivs[$i]->OrdreActiv);

		// *************************************
		// Mettre à jour dans la table
		// *************************************

		if ($v_iNouveauNumOrdre > 0)
		{
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre(),$v_iNouveauNumOrdre);

			$iIdCourant = $this->retId();

			for ($i=0; $i<$cpt; $i++)
				if ($aoNumsOrdre[$i][0] != $iIdCourant)
					$this->mettre_a_jour("OrdreActiv",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);

			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreActiv",($i+1),$aoNumsOrdre[$i][0]);

		return TRUE;
	}

	/**
	 * Retourne l'id de la première sous-activité
	 * 
	 * @return	l'id de la première sous-activité
	 */
	function retIdPremierePage ()
	{
		$sRequeteSql = "SELECT SousActiv.IdSousActiv"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." WHERE Activ.IdActiv='".$this->retId()."'"
			." AND SousActiv.PremierePageSousActiv='1'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		return (is_object($oEnreg) && $oEnreg->IdSousActiv > 0 ? $oEnreg->IdSousActiv : 0);
	}
	
	/**
	 * Retourne l'id de l'activité précédente celle-ci
	 * 
	 * @return	l'id de l'activité précédente celle-ci
	 */
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeActivs()) < 0)
			return 0;
		$cpt--;
		return ($cpt < 0 ?  0 : $this->aoActivs[$cpt]->IdActiv);
	}
	
	/**
	 * Retourne un tableau à 2 dimensions contenant l'intitulé d'une activité
	 * @todo ce système sera modifié pour l'internationalisation de la plate-forme
	 * 
	 * @return	le mot français utilisé pour désigner une activité
	 */
	function retTypes ()
	{
		return array(array(0,INTITULE_ACTIV));
	}
	
	/**
	 * @return	le texte (nom) qui désigne ce niveau de la formation (formation, module, rubrique, etc)
	 */
	function retTexteNiveau()
	{
		return INTITULE_ACTIV;
	}
	
	/**
	 * Retourne un tableau à 2 dimensions contenant les modalités d'une activité
	 * 
	 * @return	la liste des différentes modalités pour une activité
	 */
	function retListeModalites ()
	{
		return array(
			array(MODALITE_INDIVIDUEL,"individuel"),
			array(MODALITE_PAR_EQUIPE,"par &eacute;quipe"));
	}

	/**
	 * @return	le dossier associé à cette activité, donc celui où se trouvent ses fichiers associés
	 */	
	function retDossier()
	{
		$f = new FichierInfo(dir_cours($this->retId(), $this->retIdFormation()));
		return $f->retChemin();
	}
}

?>
