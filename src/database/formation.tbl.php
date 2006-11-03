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
 * @file	formation.tbl.php
 * 
 * Contient la classe de gestion des formations, en rapport avec la DB
 * 
 * @date	2002/03/01
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 * @author	Jérôme TOUZE
 */

require_once(dir_database("module.tbl.php"));
require_once(dir_code_lib("tri.inc.php"));
require_once(dir_lib("tar.class.php", TRUE));

define("INTITULE_FORMATION","Formation"); /// Titre qui désigne le niveau racine de la structure d'une formation 	@enum INTITULE_FORMATION

/**
 * Gestion des formations, et encapsulation de la table Formation de la DB
 */
class CFormation
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB
	
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $oAuteur;				///< Objet de type CPersonne qui contiendra les renseignements de la personne qui a créé cette formation
	
	var $aoFormations;			///< Tableau rempli par certaines fonctions, contenant une liste de formations
	var $aoModules;				///< Tableau rempli par #initModules(), contenant une liste de modules
	
	var $aoConcepteurs;			///< Tableau rempli par #initConcepteurs(), contenant tous les concepteurs inscrits aux cours de cette formation
	var $aoInscrits;			///< Tableau rempli par #initInscrits(), contenant tous les personnes inscrites à cette formation
	var $oModuleCourant;		///< Objet de type CModule contenant un module
	
	var $aoGlossaires;			///< Tableau rempli par #initGlossaires(), contenant une liste de glossaires de cette formation
	var $aoElementsGlossaire;	///< Tableau rempli par #initElementsGlossaire(), contenant une liste des éléments d'un glossaire @todo La table n'existe pas, travail commencé mais pas terminé...
	
	var $sTri;					///< Variable de type string contenant le texte pour trier une requete sql
	
	var $ORDRE=12;				///< Pseudo-constante qui indique qu'une opération doit etre réalisée par rapport au champ OrdreForm
	var $TYPE=13;				///< Pseudo-constante qui indique qu'une opération doit etre réalisée par rapport au champ TypeForm
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CFormation (&$v_oBdd,$v_iIdForm=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdForm;
		
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
			$this->iId = $this->oEnregBdd->IdForm;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Formation"
				." WHERE IdForm='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Efface les valeurs contenues dans \c iId, \c oBdd, \c oEnregBdd, \c aoFormations
	 */
	function detruire ()
	{
		$this->iId = NULL;
		$this->oBdd = NULL;
		$this->oEnregBdd = NULL;
		
		$this->aoFormations = NULL;
	}
	
	/**
	 * Réinitialise l'objet \c oEnregBdd avec la formation courante
	 */
	function rafraichir ()
	{
		if ($this->retId() > 0)
			$this->init();
	}
	
	/**
	 *  Verrouille les tables en relation avec la table Formation
	 */
	function verrouillerTables ()
	{
		$sRequeteSql = "LOCK TABLES"
			." Formation WRITE"
			.", Module WRITE"
			.", Intitule WRITE"
			.", Module_Rubrique WRITE"
			.", Activ WRITE"
			.", SousActiv WRITE"
			.", Forum WRITE"
			.", SujetForum WRITE"
			.", MessageForum WRITE"
			.", Formation_Inscrit WRITE"
			.", Formation_Tuteur WRITE"
			.", Formation_Concepteur WRITE"
			.", Formation_Resp WRITE"
			.", Module_Concepteur WRITE"
			.", Module_Tuteur WRITE"
			.", Module_Inscrit WRITE"
			.", SousActiv_SousActiv WRITE"
			.", ".CRessourceSousActiv::verrouillerTables(FALSE)
			.", ".CEquipe::verrouillerTables(FALSE)
			.", Chat WRITE"
			/*.", SousActiv_Glossaire WRITE"*/;
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne soit le numero d'ordre maximum ou soit le type maximum des formations
	 * 
	 * @param	v_iChamp	si \c $this->ORDRE, le maximum sera sur le champ OrdreForm, si \c $this->TYPE, il sera sur TypeForm
	 * 
	 * @return	le nombre maximum 
	 */
	function retValeurMax ($v_iChamp)
	{
		switch ($v_iChamp)
		{
			case $this->ORDRE: $sChampMax = "OrdreForm"; break;
			case $this->TYPE:  $sChampMax = "TypeForm"; break;
			default: return;
		}
		$sRequeteSql = "SELECT MAX({$sChampMax}) FROM Formation";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iMax = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iMax;
	}
	
	/**
	 * Ajoute une nouvelle formation.
	 *
	 * @param	v_sNomForm			le nom de la formation
	 * @param	v_sDescrForm		la description de la formation
	 * @param	v_iInscrSpontForm	si \c 1 alors tous les étudiants sont inscrits automatiquement à tous les cours
	 * @param	v_iIdPers			le numéro d'identifiant de la personne qui vient de créer cette formation.
	 */
	function ajouter ($v_sNomForm=NULL,$v_sDescrForm=NULL,$v_iInscrSpontForm=1,$v_iIdPers=0)
	{
		if (empty($v_sNomForm))
			$v_sNomForm = INTITULE_FORMATION." sans nom";
		
		$this->oBdd->executerRequete("LOCK TABLES Formation WRITE");
		
		$sRequeteSql = "INSERT INTO Formation SET"
			." IdForm=NULL"
			.", NomForm='".MySQLEscapeString($v_sNomForm)."'"
			.", DescrForm='".MySQLEscapeString($v_sDescrForm)."'"
			.", DateDebForm=NOW()"
			.", DateFinForm=NOW()"
			.", StatutForm='".STATUT_FERME."'"
			.", InscrSpontForm='0'"
			.", InscrAutoModules='{$v_iInscrSpontForm}'"
			.", InscrSpontEquipeF='0'"
			.", NbMaxDsEquipeF='10'"
			.", OrdreForm='".($this->retValeurMax($this->ORDRE) + 1)."'"
			.", TypeForm='".($this->retValeurMax($this->TYPE) + 1)."'"
			.", VisiteurAutoriser='0'"
			.", IdPers='{$v_iIdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->oBdd->executerRequete("UNLOCK TABLES");
		$this->init();
		$this->associerResponsable($v_iIdPers);
		
		return $this->iId;
	}
	
	/**
	 * Ajoute la personne qui vient de créer une nouvelle formation dans la table des reponsables de formation
	 *
	 * @param	v_iIdPers	l'id de la personne
	 */
	function associerResponsable ($v_iIdPers)
	{
		if (($iIdForm = $this->retId()) < 1 || $v_iIdPers < 1)
			return;
		
		$sRequeteSql = "REPLACE INTO Formation_Resp SET"
			." IdForm='{$iIdForm}'"
			.", IdPers='{$v_iIdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Permet de connaitre le numero d'ordre maximum des formations
	 * 
	 * @return	le numéro d'ordre maximum
	 */
	function retNumOrdreMax () { return $this->retValeurMax($this->ORDRE); }
	
	/**
	 * Copie la formation courante vers une autre
	 * 
	 * @param	v_bRecursive si \c true (par défaut), copie aussi tous les modules
	 * 
	 * @return	l'id de la nouvelle formation
	 */
	function copier($v_bRecursive = TRUE, $v_sExportation = NULL)
	{
		$iIdForm = $this->copierFormation($v_sExportation);
		
		if ($iIdForm < 1 && empty($v_sExportation))
			return 0;
		
		// ---------------------
		// Copier les ressources de la formation actuelle
		// vers la nouvelle formation
		// ---------------------
		$srcRep = dir_formation($this->retId());
		$dstRep = dir_formation($iIdForm);
		
		if (is_null($v_sExportation))
		{
			@mkdir($dstRep, 0744);
		}
		else
		{
			global $sSqlExportForm, $oArchiveExport;
			$sSqlExportForm = "";
			$oArchiveExport = NULL;
			$r = NULL;
			
			$sRepTemp = substr(dir_tmp(uniqid('f'.$this->retId().'-'), TRUE), 0, -1);
			@mkdir($sRepTemp, 0744);
			$oArchiveExport = new CTar("{$sRepTemp}/f".$this->retId().".tar.gz");
		}
			
		$handle = @opendir($srcRep);
		
		while ($fichier = @readdir($handle))
		{
			if ($fichier != "." && $fichier != ".." && !strstr($fichier, "activ_"))
				copyTree(($srcRep.$fichier), ($dstRep.$fichier), $v_sExportation, &$oArchiveExport);
		}
		
		@closedir($handle);
		
		// ---------------------
		// Copier tous les modules
		// ---------------------
		if ($v_bRecursive)
			$this->copierModules($iIdForm, $v_sExportation);
		
		if (empty($v_sExportation))
		{
			return $iIdForm;
		}
		else
		{
			//$r = $sSqlExportForm;
			
			$oArchiveExport->defModifsChemins('', dir_formation());
			$r = $oArchiveExport->creerArchive();
			
			$sFichierSql = "{$sRepTemp}/export.sql";
			$f = fopen($sFichierSql, 'wb+');
			if ($f !== FALSE)
			{
				fwrite($f, $sSqlExportForm);
				fclose($f);
			}
			$oArchiveExport->defModifsChemins('sql', $sRepTemp);
			$oArchiveExport->majArchive($sFichierSql);
			
			@unlink($sFichierSql);
			
			if ($r == 1)
			{
				return $oArchiveExport->retCheminArchive();
			}
			else
			{
				@unlink("{$sRepTemp}/f".$this->retId().".tar.gz");
				@rmdir($sRepTemp);
				return $r;
			}
		}
	}
	
	/**
	 * Ajoute une copie de la formation actuelle dans la DB
	 * 
	 * @return	l'id de la nouvelle formation
	 */
	function copierFormation($v_sExportation = NULL)
	{
		global $sSqlExportForm;
		
		$sRequeteSql = "INSERT INTO Formation SET"
			." IdForm=".(empty($v_sExportation)?"NULL":"'".$this->retId()."'")
			.", NomForm='".MySQLEscapeString($this->retNom())."'"
			.", DescrForm='".MySQLEscapeString($this->retDescr())."'"
			.", DateDebForm=NOW()"
			.", DateFinForm=NOW()"
			.", StatutForm='".STATUT_FERME."'"
			.", OrdreForm='".($this->retNumOrdreMax() + 1)."'"
			.", TypeForm='".$this->retType()."'"
			.", IdPers='{$this->oEnregBdd->IdPers}'";
		
		if ($v_sExportation)
		{
			$sSqlExportForm .= $sRequeteSql . ";\n\n";
			$sSqlExportForm .= "SET @iIdFormationCourante := LAST_INSERT_ID();\n\n";
			
			return -1;
		}
		else
		{
			$this->oBdd->executerRequete($sRequeteSql);
			
			return $this->oBdd->retDernierId();
		}
	}
	
	/**
	 * Copie tous les modules de la formation courante dans une autre
	 * 
	 * @param	v_iIdForm l'id de la formation de destination
	 */
	function copierModules($v_iIdForm, $v_sExportation = NULL)
	{
		$this->initModules();
		foreach ($this->aoModules as $oModule)
			$oModule->copier($v_iIdForm, TRUE, $v_sExportation);
		$this->aoModules = NULL;
	}
	
	/**
	 * Efface la totalité d'une formation
	 * 
	 * @return	l'id de la formation précédant celle-ci
	 */
	function effacer ()
	{
		if ($this->retId() < 1)
			return;
		
		$this->effacerEvenements();
		
		$this->effacerEtudiants();
		$this->effacerTuteurs();
		$this->effacerConcepteurs();
		$this->effacerResponsables();
		
		// Effacer les équipes
		$this->effacerEquipes();
		
		// Effacer tous les modules qui appartiennent à cette formation
		$this->effacerModules();
		
		// Effacer cette formation
		$this->effacerFormation();
		
		if (PHP_OS === "Linux")
			exec("rm -rf ".dir_formation($this->retId(),NULL,TRUE));
		
		$this->redistNumsOrdre();
		
		$iIdFormPrecedent = $this->retIdFormPrecedente();
		
		unset($this->iId,$this->oEnregBdd);
		
		return $iIdFormPrecedent;
	}
	
	/**
	 * Efface la formation dans la DB
	 */
	function effacerFormation ()
	{
		$sRequeteSql = "DELETE FROM Formation"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface tous les évenements relatifs à la formation
	 */
	function effacerEvenements ()
	{
		require_once(dir_database("evenement.tbl.php"));
		$oEven = new CEvenement($this->oBdd);
		$oEven->defIdFormation($this->retId());
		$oEven->effacer();
	}
	
	/**
	 * Initialise un tableau contenant les équipes relatives à la formation, et initialise également les membres de 
	 * chaque équipe
	 * 
	 * @return	le nombre d'équipes de cette formation
	 */
	function initEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$iNbrEquipes = $oEquipe->initEquipes($this->retId(),NULL,NULL,NULL,NULL,TRUE);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbrEquipes;
	}
	
	/**
	 * Initialise un tableau contenant soit les personnes inscrites dans une équipe de cette formation, 
	 * soit les personnes inscrites à cette formation qui ne font partie d'aucune équipe
	 * 
	 * @param	v_bAppartenirEquipe si \c true (par défaut) le tableau est rempli par les personnes qui appartiennent à 
	 *								une équipe de cette formation, si \c false il est rempli par les personnes qui 
	 * 								n'appartiennent à aucune équipe de cette formation (mais qui y sont inscrites)
	 * @param	v_iSensTri 			indique si un tri doit être effectué ainsi que son sens (croissant par défaut)
	 * 
	 * @return	le nombre de personnes(membres) insérées dans le tableau
	 * 
	 * @todo	on peut sûrement améliorer la première requête sql, elle met 7 sec a s'exécuter sur la version "production"
	 * 			pour ne retourner que peu d'enregistrements(+-30)
	 */
	function initMembres ($v_bAppartenirEquipe=TRUE,$v_iSensTri=TRI_CROISSANT)
	{
		$iIdxMembre = 0;
		$this->aoMembres = array();
		
		$iIdForm = $this->retId();
		
		if ($v_bAppartenirEquipe)
			$sRequeteSql = "SELECT Personne.*"
				." FROM Personne"
 				." LEFT JOIN Equipe_Membre USING (IdPers)"
 				." LEFT JOIN Equipe USING (IdEquipe)"
 				." WHERE Equipe.IdForm='{$iIdForm}' AND Equipe.IdMod='0'";
		else
			$sRequeteSql = "SELECT Personne.*"
				." FROM Formation_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Equipe.IdForm=Formation_Inscrit.IdForm"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdForm='{$iIdForm}' AND Equipe.IdMod='0'"
				." GROUP BY Personne.IdPers	HAVING COUNT(Equipe_Membre.IdEquipe)='0'";
		
		if ($v_iSensTri <> PAS_TRI)
			$sRequeteSql .= " ORDER BY Personne.Nom ".($v_iSensTri == TRI_DECROISSANT ? "DESC" : "ASC");
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMembres[$iIdxMembre] = new CPersonne($this->oBdd);
			$this->aoMembres[$iIdxMembre]->init($oEnreg);
			$iIdxMembre++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxMembre;
	}
	
	/**
	 * Initialise un tableau contenant la liste des glossaires appartenant à une formation.
	 *
	 * @return le nombre totale de glossaires trouvés ou zéro dans le cas contraire.
	 */
	function initGlossaires ()
	{
		$iIdxGlossaire = 0;
		$this->aoGlossaires = array();
		
		$sRequeteSql = "SELECT * FROM Glossaire"
			." WHERE IdForm='".$this->retId()."'"
			." ORDER BY TitreGlossaire";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoGlossaires[$iIdxGlossaire] = new CGlossaire($this->oBdd);
			$this->aoGlossaires[$iIdxGlossaire]->init($oEnreg);
			$iIdxGlossaire++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxGlossaire;
	}
	
	/**
	 * Ajoute un nouveau glossaire à la formation
	 * 
	 * @param	v_sTitreGlossaire le nom du glossaire
	 * @param	v_iIdPers le numéro d'identifiant de la personne qui vient de créer ce glossaire
	 */
	function ajouterGlossaire ($v_sTitreGlossaire,$v_iIdPers)
	{
		$oGlossaire = new CGlossaire($this->oBdd);
		$oGlossaire->ajouter($v_sTitreGlossaire,$this->retId(),$v_iIdPers);
	}
	
	/**
	 * @todo La table GlossaireElement n'existe pas, travail commencé mais pas terminé...
	 * 		les éléments d'un glossaire
	 */
	function initElementsGlossaire ($v_iIdGlossaire=NULL)
	{
		$iIdxElems = 0;
		$this->aoElementsGlossaire = array();
		
		if ($v_iIdGlossaire < 1)
			$v_iIdGlossaire = 0;
		
		$sRequeteSql = "SELECT GlossaireElement.*"
			.", Glossaire_GlossaireElement.IdGlossaire AS estSelectionne"
			." FROM GlossaireElement"
			." LEFT JOIN Glossaire_GlossaireElement ON GlossaireElement.IdGlossaireElement=Glossaire_GlossaireElement.IdGlossaireElement"
			." AND Glossaire_GlossaireElement.IdGlossaire='{$v_iIdGlossaire}'"
			." WHERE GlossaireElement.IdForm='".$this->retId()."'"
			." ORDER BY GlossaireElement.TitreGlossaireElement ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoElementsGlossaire[$iIdxElems] = new CGlossaireElement($this->oBdd);
			$this->aoElementsGlossaire[$iIdxElems]->init($oEnreg);
			$iIdxElems++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxElems;
	}
	
	/**
	 * @todo La table GlossaireElement n'existe pas, travail commencé mais pas terminé...
	 * 		les éléments d'un glossaire
	 */
	function ajouterElementsGlossaire ($v_iIdGlossaire,$v_aiIdsElementsGlossaire)
	{
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdsElementsGlossaire as $iIdGlossaireElement)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$v_iIdGlossaire}','{$iIdGlossaireElement}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "INSERT INTO Glossaire_GlossaireElement"
				." (IdGlossaire,IdGlossaireElement)"
				." VALUES {$sValeursRequete};";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	/**
	 * @todo La table n'existe pas, travail commencé mais pas terminé...
	 * 		les éléments d'un glossaire
	 */
	function effacerElementsGlossaire ($v_iIdGlossaire)
	{
		$sRequeteSql = "DELETE FROM Glossaire_GlossaireElement"
			." WHERE IdGlossaire='{$v_iIdGlossaire}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface toutes les équipes d'une formation
	 */
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_FORMATION,$this->iId);
	}
	
	/**
	 * @deprecated Ne semble pas/plus utilisé ???
	 * 
	 * @return	\c false
	 */
	function effacerModelesEquipes ()
	{
		return FALSE;
	}
	
	/**
	 * Efface logiquement une formation, elle peut etre ensuite effacé physiquement (CFormation#effacer )
	 * 
	 * @return	l'id de la formation précédant celle-ci
	 */
	function effacerLogiquement ()
	{
		$this->defStatut(STATUT_EFFACE);
		$this->defNumOrdre(0);
		$this->redistNumsOrdre();
		return $this->retIdFormPrecedente();
	}
	
	/**
	 * Efface tous les responsables(Table Formation_Resp) de la formation
	 */
	function effacerResponsables ()
	{
		$sRequeteSql = "DELETE FROM Formation_Resp"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface tous les concepterus(Table Formation_Concepteur) de la formation
	 */
	function effacerConcepteurs ()
	{
		$sRequeteSql = "DELETE FROM Formation_Concepteur"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface tous les tuteurs(Table Formation_Tuteur) de la formation
	 */
	function effacerTuteurs ()
	{
		$sRequeteSql = "DELETE FROM Formation_Tuteur"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface tous les étudiants(Table Formation_Inscrit) de la formation
	 */
	function effacerEtudiants ()
	{
		$sRequeteSql = "DELETE FROM Formation_Inscrit"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 *  Efface tous les modules de la formation
	 */
	function effacerModules ()
	{
		$iNbrModules = $this->initModules();
		
		for ($idx=0; $idx<$iNbrModules; $idx++)
			$this->aoModules[$idx]->effacer();
		
		unset($this->aoModules);
	}
	
	/**
	 * Retourne l'id de la formation précédant celle-ci
	 * 
	 * @return	l'id de la formation précédente
	 */
	function retIdFormPrecedente ()
	{
		$iNumOrdre = $this->retNumOrdre();
		
		if ($iNumOrdre>1) $iNumOrdre--;
		
		$iIdForm = 0;
		
		while ($iNumOrdre>0)
		{
			$sRequeteSql = "SELECT * FROM Formation"
				." WHERE OrdreForm='{$iNumOrdre}'"
				." AND StatutForm<>'".STATUT_EFFACE."'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$iIdForm = $oEnreg->IdForm;
				break;
			}
			
			$this->oBdd->libererResult($hResult);
			$iNumOrdre--;
		}
		
		return $iIdForm;
	}
	
	/**
	 * Initialise un tableau avec les étudiants inscrits à la formation
	 * 
	 * @param	v_sModeTri si \c "ASC" (par défaut), tri croissant sur le nom, si \c "DESC" tri décroissant
	 * 
	 * @return	le nombre de personnes(étudiants) insérées dans le tableau
	 */
	function initInscrits ($v_sModeTri="ASC")
	{
		$iIdxInscrit = 0;
		$this->aoInscrits = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Formation_Inscrit"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Formation_Inscrit.IdForm='".$this->retId()."'"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete ($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoInscrits[$iIdxInscrit] = new CPersonne($this->oBdd);
			$this->aoInscrits[$iIdxInscrit]->init($oEnreg);
			$iIdxInscrit++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxInscrit;
	}
	
	/**
	 * Permet de connaître le nombre de formation effacée logiquement
	 * 
	 * @return	le nombre de formation ayant le statut effacé
	 */
	function retNombreLignes ()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Formation"
			." WHERE StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNbrLignes;
	}
	
	/**
	 * Initialise un tableau avec les responsables (Formation_Resp) de la formation
	 * 
	 * @return	le nombre de personnes(responsables) insérées dans le tableau
	 */
	function initResponsables ()
	{
		$iIdxResp = 0;
		$this->aoResponsables = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Formation_Resp"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Formation_Resp.IdForm='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete ($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoResponsables[$iIdxResp] = new CPersonne($this->oBdd);
			$this->aoResponsables[$iIdxResp]->init($oEnreg);
			$iIdxResp++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxResp;
	}
	
	/**
	 * Initialise le module courant (\c oModuleCourant) de la formation
	 * 
	 * @param	v_iIdModule l'id du module. Si le module n'appartient à la formation, l'initialisation n'a pas lieu
	 */
	function initModuleCourant ($v_iIdModule=NULL)
	{
		if ($v_iIdModule>0)
		{
			$this->oModuleCourant = new CModule($this->oBdd, $v_iIdModule);
			
			if ($this->oModuleCourant->retIdParent() != $this->retId())
				unset($this->oModuleCourant);
		}
	}
	
	/**
	 * Definit la variable du tri
	 * 
	 * @param	v_sNomChamps le nom du champ sur lequel le tri doit être effectué
	 * @param	v_sSensTri si \c "ASC" (par défaut), tri croissant sur le champ, si \c "DESC" tri décroissant
	 */
	function defTrier ($v_sNomChamps=NULL,$v_sSensTri=NULL)
	{
		if ($v_sNomChamps == "types")
			$this->sTri = " ORDER BY TypeForm";
		else if ($v_sNomChamps == "noms")
			$this->sTri = " ORDER BY NomForm";
		else
			$this->sTri = " ORDER BY OrdreForm";
		
		$this->sTri .= " ".(isset($v_sSensTri) ? $v_sSensTri : "ASC");
	}
	
	/**
	 * Retourne la variable tri
	 * 
	 * @return	la variable tri, et l'initialise si besoin
	 */
	function retTrier ()
	{
		if (empty($this->sTri))
			$this->defTrier();
		
		return $this->sTri;
	}
	
	/**
	 * Permet d'ajouter à la variable \c tri un espace temporel (une selection entre 2 dates)
	 * 
	 * @param	v_dDebut date de début au format SQL (YYYY-MM-DD)
	 * @param	v_dFin date de fin
	 */
	function defTrierParAnnee ($v_dDebut,$v_dFin)
	{
		$this->sTri = " AND DateDebForm>=\"{$v_dDebut}\""
			." AND DateDebForm<=\"{$v_dFin}\"".$this->sTri;
	}
	
	/**
	 *  Initialise la variable \c tri avec un tri sur le type de formation (GROUP BY TypeForm) et lui ajoute son sens
	 * 
	 * @param	v_iSensTri si \c "ASC" (par défaut), tri croissant sur le champ, si \c "DESC" tri décroissant
	 */
	function defTrierParType ($v_iSensTri=NULL)
	{
		$this->sTri = " GROUP BY TypeForm"
			." ".(isset($v_iSensTri) ? $v_iSensTri : "ASC");
	}
	
	/**
	 * Permet de définir l'accès au visiteur de la formation
	 * 
	 * @param	v_bAutoriserVisiteur peut prendre la valeur 1 ou 0 (pas d'accès au visiteur) 
	 */
	function defVisiteurAutoriser ($v_bAutoriserVisiteur)
	{
		$this->mettre_a_jour("VisiteurAutoriser",$v_bAutoriserVisiteur);
	}
	
	/**
	 * Retourne l'autorisation d'accès des visiteurs à la formation
	 * 
	 * @return	1(accès aux visiteurs) ou 0 (pas d'accès aux visiteurs)
	 */
	function accessibleVisiteurs () { return $this->oEnregBdd->VisiteurAutoriser; }
	
	/**
	 * Initialise un tableau contenant toutes les formations ayant le statut ouvert ou fermé, il n'insère que les formations
	 * dont la personne est responsable
	 * 
	 * @param	v_iIdPers l'id de la personne qui veut copier une formation
	 * 
	 * @return	le nombre de formation insérées dans le tableau
	 */
	function initFormationsPourCopie ($v_iIdPers)
	{
		$idx = 0;
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT Formation.* FROM Formation"
			." LEFT JOIN Formation_Resp ON Formation.IdForm=Formation_Resp.IdForm"
			." WHERE Formation.StatutForm IN ('".STATUT_OUVERT."','".STATUT_FERME."')"
			." AND Formation_Resp.IdPers='{$v_iIdPers}'"
			.$this->retTrier();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$idx] = new CFormation($this->oBdd);
			$this->aoFormations[$idx]->init($oEnreg);
			$idx++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $idx;
	}
	
	/**
	 * Initialise un tableau contenant toutes les formations
	 * 
	 * @param	v_bAdministrateur si \c false (defaut) on ne voie que les formations ayant le statut ouvert ou fermé
	 * 
	 * @return	le nombre de formation insérées dans le tableau
	 */
	function initFormations ($v_bAdministrateur=FALSE)
	{
		$idx = 0;
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT * FROM Formation"
			.($v_bAdministrateur ? NULL : " WHERE StatutForm IN ('".STATUT_OUVERT."','".STATUT_FERME."')")
			.$this->retTrier();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$idx] = new CFormation($this->oBdd);
			$this->aoFormations[$idx]->init($oEnreg);
			$idx++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $idx;
	}
	
	/**
	 * Initialise un tableau contenant la liste des modules de la formation
	 * 
	 * @param	v_iIdPers				le numéro d'identifiant de la personne
	 * @param	v_iIdStatutUtilisateur	l'id du statut de la personne utilisé comme critère pour la recherche. 
	 * 									La signification de ce paramètre est modifiée par \p v_bRechStricte.
	 * 									Si le paramètre n'est pas fourni, tous les modules de la formation seront récupérés
	 * @param	v_bRechStricte			si \true et \p v_iIdStatutUtilisateur fourni, alors seul les modules pour lesquels
	 * 									la personne a exactement le statut demandé seront récupérés. Si \c false, les
	 * 									modules pour lesquels la personne a un statut inférieur ou égal à celui demandé
	 * 									seront également récupérés
	 * 
	 * @return	le nombre de module insérés dans le tableau
	 */
	function initModules ($v_iIdPers=0,$v_iIdStatutUtilisateur=NULL,$v_bRechStricte=FALSE)
	{
		include_once(dir_database("modules.class.php"));
		
		$iIdxModule = 0;
		$this->aoModules = array();
		
		$oModules = new CModules($this->oBdd,$this->retId(),$this->aoModules);
		
		if (isset($v_iIdStatutUtilisateur) && $v_bRechStricte)
			$iIdxModule = $oModules->initModulesParStatut($v_iIdPers,$v_iIdStatutUtilisateur);
		else if (isset($v_iIdStatutUtilisateur))
			$iIdxModule = $oModules->initModulesUtilisateur($v_iIdPers,$v_iIdStatutUtilisateur,$this->retInscrAutoModules());
		else
			$iIdxModule = $oModules->initTousModules();
		
		return $iIdxModule;
	}
	
	/**
	 * Vérifie si une équipe à déja déposé un ou plusieurs documents
	 * @deprecated Ne semble pas/plus utilisé ???
	 * 
	 * @param	v_iIdEquipe 	l'id de l'équipe à vérifier
	 * @param	v_iIdPers		l'id de la personne(optionnel)
	 * 
	 * @return	\c true si l'équipe a déjà déposé des documents
	 */
	function verifEquipe ($v_iIdEquipe,$v_iIdPers=NULL)
	{
		$sRequeteSql = "SELECT Equipe_Membre.IdPers FROM Ressource"
			." LEFT JOIN Equipe_Membre USING (IdPers)"
			." LEFT JOIN Equipe USING (IdEquipe)"
			." LEFT JOIN Ressource_SousActiv ON Ressource.IdRes=Ressource_SousActiv.IdRes"
			." LEFT JOIN SousActiv USING (IdSousActiv)"
			." LEFT JOIN Activ USING (IdActiv)"
			." WHERE Activ.ModaliteActiv='".MODALITE_PAR_EQUIPE."'"
			." AND Equipe.IdEquipe='{$v_iIdEquipe}'"
			.(isset($v_iIdPers) ? " AND Ressource.IdPers='$v_iIdPers'" : NULL);
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		
		$this->oBdd->libererResult($hResult);
		
		return (!empty($oEnreg));
	}
	

	/**
	 * Initialise un tableau contenant tous les concepteurs inscrits aux cours de cette formation
	 * 
	 * @param	v_sModeTri v_sModeTri si \c "ASC" (par défaut), tri croissant sur le nom, si \c "DESC" tri décroissant
	 * 
	 * @return	le nombre de concepteurs insérés dans le tableau
	 */
	function initConcepteurs ($v_sModeTri="ASC")
	{
		$iIdxConcepteur = 0;
		$this->aoConcepteurs = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Concepteur USING (IdPers)"
			." WHERE Formation_Concepteur.IdForm='".$this->retId()."'"
			." GROUP BY Personne.IdPers"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$iIdxConcepteur] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$iIdxConcepteur]->init($oEnreg);
			$iIdxConcepteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxConcepteur;
	}
	

	/**
	 * Initialise un tableau contenant tous les tuteurs inscrits aux cours de cette formation
	 * 
	 * @param	v_sModeTri si \c "ASC" (par défaut), tri croissant sur le nom, si \c "DESC" tri décroissant
	 * 
	 * @return	le nombre de tuteurs insérés dans le tableau
	 */
	function initTuteurs ($v_sModeTri="ASC")
	{
		$iIdxTuteur = 0;
		$this->aoTuteurs = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Tuteur USING (IdPers)"
			." WHERE Formation_Tuteur.IdForm='".$this->retId()."'"
			." GROUP BY Personne.IdPers"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTuteurs[$iIdxTuteur] = new CPersonne($this->oBdd);
			$this->aoTuteurs[$iIdxTuteur]->init($oEnreg);
			$iIdxTuteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxTuteur;
	}
	

	/** @name Fonctions de définition des champs pour cette formation */
	//@{
	function defNom ($v_sNomForm)
	{		
		$v_sNomForm = MySQLEscapeString($v_sNomForm);
		
		if (empty($v_sNomForm))
			$v_sNomForm = INTITULE_FORMATION." sans nom";
		
		$this->mettre_a_jour("NomForm",$v_sNomForm);
	}
	
	function defDescr ($v_sDescrForm)
	{		
		$this->mettre_a_jour("DescrForm",$v_sDescrForm);
	}
	
	function defInscrAutoModules ($v_bInscrAutoModules=TRUE)
	{
		if (is_numeric($v_bInscrAutoModules))
			$this->mettre_a_jour("InscrAutoModules",$v_bInscrAutoModules);
	}

	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreForm",$v_iNumOrdre);
	}
	
	function defType ($v_iType)
	{
		if (is_numeric($v_iType))
			$this->mettre_a_jour("TypeForm",$v_iType); 
	}

	function defIdPers ($v_iIdPers)
	{
		$this->oEnregBdd->IdPers = $v_iIdPers;
	}

	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutForm",$v_iStatut);
	}
	//@}


	/** @name Fonctions de lecture des champs pour cette formation */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retDateDeb ()
	{
		list($sDate) = explode(" ",$this->oEnregBdd->DateDebForm);
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$sDate);
	}
	
	function retDateFin ()
	{
		list($sDate) = explode(" ",$this->oEnregBdd->DateFinForm);
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$sDate);
	}
	
	function retStatut ()
	{
		return $this->oEnregBdd->StatutForm;
	}
	
	function retType () { return $this->oEnregBdd->TypeForm; }
	function retInscrSpontForm () { return $this->oEnregBdd->InscrSpontForm; }
	function retInscrAutoModules () { return $this->oEnregBdd->InscrAutoModules; }
	function retInscrSpontEquipe () { return $this->oEnregBdd->InscrSpontEquipeF; }
	function retNbMaxDsEquipe () { return $this->oEnregBdd->NbMaxDsEquipeF; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreForm; }
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? mb_convert_encoding($this->oEnregBdd->NomForm,"HTML-ENTITIES","UTF-8") : $this->oEnregBdd->NomForm); }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	
	function retDescr ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? mb_convert_encoding($this->oEnregBdd->DescrForm,"HTML-ENTITIES","UTF-8") : $this->oEnregBdd->DescrForm);
	}
	//@}

	
	/**
	 * Retourne le nom par défaut d'une formation
	 * 
	 * @return	le nom par défaut d'une formation
	 */
	function retNomParDefaut ()
	{
		return INTITULE_FORMATION." sans nom";
	}

	/**
	 * Met à jour un champ de la table formation
	 * 
	 * @param	v_sNomChamp		le nom du champ à mettre à jour
	 * @param	v_mValeurChamp	la nouvelle valeur du champ
	 * @param	v_iIdForm		l'id de la formation
	 * 
	 * @return	\c true si il a mis à jour le champ dans la DB
	 */
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdForm=0)
	{
		if ($v_iIdForm < 1)
			$v_iIdForm = $this->retId();
		
		if ($v_iIdForm < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Formation SET"
			." {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'"
			." WHERE IdForm='{$v_iIdForm}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}

	/**
	 * Redistribue les numéros d'ordre des formations
	 * 
	 * @param	v_iNouveauNumOrdre le nouveau numéro d'ordre de la formation courante
	 * 
	 * @return	\c true si les numéros ont bien été modifiés
	 */
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if (isset($v_iNouveauNumOrdre) && $v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;
		
		if (($cpt = $this->initFormations()) < 0)
			return FALSE;
		
		// *************************************
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		// *************************************
		
		$aoNumsOrdre = array();
		
		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array($this->aoFormations[$i]->retId(),$this->aoFormations[$i]->retNumOrdre());
		
		// *************************************
		// Mettre à jour dans la table avec les nouveaux numéros d'ordre
		// *************************************
		
		if ($v_iNouveauNumOrdre > 0)
		{
			// *************************************
			// Appel à une fonction externe pour une redistribution des numéros d'ordre
			// *************************************
			
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre(),$v_iNouveauNumOrdre);
			
			$iIdFormCourante = $this->retId();
			
			for ($i=0; $i<$cpt; $i++)
				if ($aoNumsOrdre[$i][0] != $iIdFormCourante)
					$this->mettre_a_jour("OrdreForm",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);
			
			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
		{
			// Cette boucle est utilisée, par exemple, lorsqu'on efface une ligne de la table
			// et nous voulons simplement remettre de l'ordre (de 1 à n)
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreForm",($i+1),$aoNumsOrdre[$i][0]);
		}
		
		return TRUE;
	}
	
	/**
	 * Initialise un tableau contenant toutes les formations ayant le statut effacé
	 * 
	 * @return	le nombre de formation insérées dans le tableau
	 */
	function initFormationsEffacer ()
	{
		$iIdxForms = 0;
		
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT * FROM Formation"
			." WHERE StatutForm='".STATUT_EFFACE."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$iIdxForms] = new CFormation($this->oBdd);
			$this->aoFormations[$iIdxForms]->init($oEnreg);
			$iIdxForms++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxForms;
	}
	
	/**
	 * Vérifie si la personne est étudiante de cette formation
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est étudiante
	 */
	function verifEtudiant ($v_iIdPers)
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Formation_Inscrit"
			." WHERE IdForm='".$this->retId()."'"
			." AND IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bVerifEtudiant = ($this->oBdd->retEnregPrecis($hResult) == 1);
		$this->oBdd->libererResult($hResult);
		return $bVerifEtudiant;
	}
	
	
	 /**
	  * Initialise les informations de la personne qui a créé cette formation
	  */
	function initAuteur ()
	{
		if (is_object($this->oEnregBdd))
			$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
		else
			$this->oAuteur = NULL;
	}
	
	
	/**
	 * Vérifie si la personne est responsable de cette formation
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est responsable
	 */
	function verifResponsable ($v_iIdPers)
	{
		$bEstResponsable = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			$sRequeteSql = "SELECT Formation_Resp.*"
				." FROM Formation"
				." LEFT JOIN Formation_Resp USING (IdForm)"
				." WHERE Formation.IdForm='{$iIdForm}'"
				." AND Formation.StatutForm<>".STATUT_EFFACE
				." AND (Formation.IdPers='{$v_iIdPers}'"
				." OR Formation_Resp.IdPers='{$v_iIdPers}')"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstResponsable = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstResponsable;
	}
	
	/**
	 * Vérifie si la personne est concepteur de cours de cette formation
	 * 
	 * @param	v_iIdPers			l'id de la personne
	 * @param	v_bAuMoinsUnCours	l'utilisateur doit être au moins inscrit comme concepteur à un cours
	 * 
	 * @return	\c true si la personne est concepteur
	 */
	function verifConcepteur ($v_iIdPers,$v_bAuMoinsUnCours=TRUE)
	{
		$bEstConcepteur = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			if ($v_bAuMoinsUnCours)
				$sRequeteSql = "SELECT Module_Concepteur.IdPers"
					." FROM Module"
					." LEFT JOIN Module_Concepteur USING (IdMod)"
					." WHERE Module.IdForm='{$iIdForm}'"
					." AND Module_Concepteur.IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			else
				$sRequeteSql = "SELECT IdPers FROM Formation_Concepteur"
					." WHERE IdForm='{$iIdForm}'"
					." AND IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);		
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstConcepteur = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstConcepteur;
	}
	
	/**
	 * Vérifie si la personne est tuteur de cours de cette formation
	 * 
	 * @param	v_iIdPers			l'id de la personne
	 * @param	v_bAuMoinsUnCours	l'utilisateur doit être au moins inscrit comme concepteur à un cours
	 * 
	 * @return	\c true si la personne est tuteur
	 */
	function verifTuteur ($v_iIdPers,$v_bAuMoinsUnCours=TRUE)
	{
		$bEstTuteur = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			if ($v_bAuMoinsUnCours)
				$sRequeteSql = "SELECT Module_Tuteur.IdPers"
					." FROM Module"
					." LEFT JOIN Module_Tuteur USING (IdMod)"
					." WHERE Module.IdForm='{$iIdForm}'"
					." AND Module_Tuteur.IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			else
				$sRequeteSql = "SELECT IdPers FROM Formation_Tuteur"
					." WHERE IdForm='{$iIdForm}'"
					." AND IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstTuteur = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstTuteur;
	}
	
	/**
	 * Retourne un tableau contenant tous les statuts possibles de la personne dans cette formation 
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	un tableau contenant tous les statuts de la personne dans cette formation
	 */
	function retStatutsUtilisateur ($v_iIdPers)
	{
		$oStatutUtilisateur = new CStatutUtilisateur($this->oBdd,$v_iIdPers);
		$oStatutUtilisateur->initStatuts($this->oEnregBdd->IdForm,0,$this->oEnregBdd->InscrAutoModules);
		return $oStatutUtilisateur->aiStatuts;
	}
	
	/**
	 * Retourne le plus haut statut que la personne peut avoir dans la formation
	 * @deprecated Ne semble pas/plus utilisé ???
	 * 
	 * @param	v_iIdPers 					l'id de la personne
	 * @param	v_iStatutActuelUtilisateur	le statut les plus elevé à partir duquel on veffectue la vérification.
	 * 										Si \c null, on commence au statut le plus elevé(STATUT_PERS_ADMIN).
	 * 										La vérification se termine par le statut le plus bas(STATUT_PERS_VISITEUR)
	 * 
	 * @return	l'id de statut le plus elevé
	 */
	function retStatutHautUtilisateur ($v_iIdPers,$v_iStatutActuelUtilisateur=NULL)
	{
		$aiStatutsUtilisateur = $this->retStatutsUtilisateur($v_iIdPers);
		
		if ($v_iStatutActuelUtilisateur == NULL)
			$v_iStatutActuelUtilisateur = STATUT_PERS_ADMIN;
		
		for ($iIdxStatut=$v_iStatutActuelUtilisateur; $iIdxStatut<=STATUT_PERS_VISITEUR; $iIdxStatut++)
			if ($aiStatutsUtilisateur[$iIdxStatut])
				break;
		
		return $iIdxStatut;
	}
	
	/**
	 * Retourne un tableau à 2 dimensions contenant l'intitulé d'une formation
	 * @todo ce système sera modifié pour l'internationalisation de la plate-forme
	 * 
	 * @return	le mot français utilisé pour désigner une formation
	 */
	function retTypes ()
	{
		return array(array(0,INTITULE_FORMATION));
	}
	
	/**
	 * Retourne la liste des statuts disponibles pour une formation
	 * 
	 * @return	la liste des statuts disponibles
	 */
	function retListeStatuts ()
	{
		return array(
			array(STATUT_FERME,"Fermé"),
			array(STATUT_OUVERT,"Ouvert"),
			array(STATUT_INVISIBLE,"Invisible"),
			array(STATUT_LECTURE_SEULE,"Clôturé")
			/*array(STATUT_ARCHIVE,"Archivé")*/);
	}
}

?>
