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
 * @file	personne.tbl.php
 * 
 * Contient la classe de gestion des personnes, en rapport avec la DB
 * 
 * @date	2001/09/06
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 * @author	Jérôme TOUZE
 */

/** @name Constantes pour les genres féminin/masculin */
 //@{
define("PERSONNE_SEXE_FEMININ","F");
define("PERSONNE_SEXE_MASCULIN","M");
 //@}

/**
 * Gestion des personnes, et encapsulation de la table Personne de la DB
 */
class CPersonne
{
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la personne à récupérer dans la DB
	
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $aoPersonnes;	///< Tableau qui devrait être rempli par certaines fonctions, pour contenir une liste de personnes @deprecated Ne semble pas/plus utilisé ???
	var $aoModules;		///< Tableau rempli par #initModules(), qui contiendra la liste des modules auxquels la personne participe
	
	/**
	 * Constructeur
	 * 
	 * @param	v_oBdd	l'objet CBdd qui représente la connexion courante à la DB
	 * @param	v_iId	l'id de la personne à récupérer dans la DB. S'il est omis ou si la personne demandée n'existe 
	 * 					pas dans la DB, l'objet est créé mais ne contient aucune donnée provenant de la DB
	 * 
	 * @see	#init()
	 * 
	 * @note	Le fonctionnement du constructeur est similaire pour presque toutes les classes encapsulant une table 
	 * 			de la DB, c'est pourquoi la documentation d'autres classes renvoie à celle-ci, pour éviter d'en répéter 
	 * 			le principe général
	 */
	function CPersonne(&$v_oBdd, $v_iId = 0)
	{
		$this->oBdd = &$v_oBdd;
		
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * 
	 * @param	v_oEnregExistant	l'objet PHP représentant un enregistrement de la DB. Cet objet provient généralement 
	 * 								d'une requête antérieure, et doit avoir pour propriétés les noms des champs de la 
	 * 								table Personne. Par défaut, ce paramètre est \c null, ce qui signifie qu'une requête 
	 * 								est automatiquement effectuée dans la table Personne, à la recherche de 
	 * 								l'enregistrement qui a pour clé #iId. Si l'enregistrement est trouvé, l'objet est 
	 * 								rempli avec ce dernier (dans #oEnregBdd). En général, c'est ce qui se produit, et 
	 * 								#iId a été rempli au préalable par le constructeur, qui appelle ensuite cette 
	 * 								fonction
	 * 
	 * @note	Le fonctionnement de init() est similaire pour presque toutes les classes encapsulant une table de la 
	 * 			DB, c'est pourquoi la documentation d'autres classes renvoie à celle-ci, pour éviter d'en répéter le 
	 * 			principe général
	 */
	function init($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdPers;
		}
		else
		{
			$sRequeteSql =
				 " SELECT *, UPPER(Nom) AS Nom"
				." FROM Personne"
				." WHERE IdPers='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Initialise l'objet avec une personne de la DB sur base de critères spécifiques, pas seulement sur l'id
	 * 
	 * @param	v_asInfosPers	le tableau contenant les critères, chaque clé étant le nom du champ de la DB qui sera 
	 * 							pris en compte, et chaque valeur correspondante étant la vlaue qu'on attend pour ce 
	 * 							champ. Par exemple, si $v_asInfosPers["Nom"] = "Floquet" et $v_asInfosPers["Prenom"] = 
	 * 							"Cédric", une requête SQL sera exécutée sur table Personne pour trouver Cédric Floquet 
	 * 							dans la table
	 * 
	 * @return	le nombre de personnes trouvées répondant aux critères (bien que seule la première trouvée serve à 
	 * 			initialiser l'objet)
	 */
	function initPersonne($v_asInfosPers)
	{
		$iInitPersonne = 0;
		
		$sConditionsRequete = NULL;
		
		foreach ($v_asInfosPers as $sCle => $sValeur)
			if (isset($sValeur))
				$sConditionsRequete .= (isset($sConditionsRequete) ? " AND " : NULL)
					."{$sCle} = '{$sValeur}'";
		
		if (isset($sConditionsRequete))
		{
			$sRequeteSql = 
				 " SELECT * FROM Personne"
				." WHERE {$sConditionsRequete}"
				." LIMIT 2";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if (($iInitPersonne = $this->oBdd->retNbEnregsDsResult($hResult)) == 1)
				$this->init($this->oBdd->retEnregSuiv($hResult));
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iInitPersonne;
	}
	
	/**
	 * Insère une nouvelle personne dans la DB et retourne l'id de celle-ci. L'enregistrement créé est "vide", à part 
	 * l'id, aucune information n'est enregistrée
	 * 
	 * @return	l'id de la nouvelle personne insérée dans la DB
	 */
	function ajouter()
	{
		$sRequeteSql = "INSERT INTO Personne SET IdPers=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/**
	 * Retourne le code HTML nécessaire à la représentation d'un lien "e-mail" de la personne
	 * 
	 * @param	v_bComplet	si \c true, les nom et prénom de la personne sont utilisés comme intitulé du lien
	 * @param	v_sSujet	le texte à utiliser pour remplir automatiquement le sujet du mail lors d'un clic sur le lien
	 * @param	v_pseudo	si \c true, c'est le pseudo de la personne qui est utilisé comme intitulé du lien
	 * 
	 * @return	le code nécessaire à la représentation HTML du lien "e-mail" de la personne. Si celle-ci ne dispose pas 
	 * 			d'adresse e-mail, ce n'est pas un lien mais un texte (nom et/ou prénom, ou pseudo) non-cliquable qui est 
	 * 			retourné
	 */
	function retLienEmail($v_bComplet = FALSE, $v_sSujet = NULL, $v_sPseudo = FALSE)
	{
		if ($v_bComplet)
			$sNom = $this->retNomComplet();
		else
			$sNom = $this->retNom();
		
		if ($v_sPseudo)
			$sNom = $this->retPseudo();
		
		$sNom = trim($sNom);
		
		if (empty($sNom))
			return "Orphelin";
		
		if (isset($v_sSujet))
			$v_sSujet = "?subject=".rawurlencode($v_sSujet);
		
		if (strlen($this->retEmail()))
			return "<a class=\"avec_email\" href=\"mailto:".$this->retEmail()."$v_sSujet\""
				." title=\"Envoyer un e-mail\">{$sNom}</a>";
		else
			return "<span class=\"sans_email\">{$sNom}</span>";
	}

	/**
	 * Vérifie que le couple nom+prénom de la personne n'existe pas encore dans la DB
	 * 
	 * @return	\c true si le couple nom+prénom de cette personne n'existe pas encore dans la DB
	 */
	function estUnique()
	{
		$bEstUnique = TRUE;
		if (empty($this->oEnregBdd->IdPers))
			$currentId=0;
		else
			$currentId=$this->oEnregBdd->IdPers;
		
		$sRequeteSql = 
			 " SELECT IdPers FROM Personne"
			." WHERE Nom = '{$this->oEnregBdd->Nom}'"
			." AND Prenom = '{$this->oEnregBdd->Prenom}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$bEstUnique = ($oEnreg->IdPers == $currentId);
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstUnique;
	}
	
	/**
	 * Vérifie que le pseudo de cette personne n'existe pas encore dans la DB
	 * 
	 * @return	\c true si le pseudo de cette personne n'est pas encore utilisé dans la DB
	 */
	function estPseudoUnique()
	{
		$bEstUnique = TRUE;
		
		// Vérifier seulement dans le cas d'un ajout
		// d'un nouvel utilisateur
		$sRequeteSql =
			 " SELECT IdPers FROM Personne"
			." WHERE Pseudo='{$this->oEnregBdd->Pseudo}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$bEstUnique = ($oEnreg->IdPers == $this->oEnregBdd->IdPers);
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstUnique;
	}
	
	/**
	 * Enregistre la personne représentée par l'objet courant dans la DB. S'il elle n'existe pas encore (id), un nouvel 
	 * enregistrement est créé, sinon la personne existante voit ses infos mises à jour
	 * 
	 * @return	\c true
	 */
	function enregistrer()
	{
		$sRequeteSql = ($this->retId() > 0 ? "UPDATE Personne SET" : "INSERT INTO Personne SET")
			." Nom='".MySQLEscapeString($this->oEnregBdd->Nom)."'"
			.", Prenom='".MySQLEscapeString($this->oEnregBdd->Prenom)."'"
			.", Pseudo='{$this->oEnregBdd->Pseudo}'"
			.", Sexe='{$this->oEnregBdd->Sexe}'"
			.", Email='{$this->oEnregBdd->Email}'"
			.", NumTel='{$this->oEnregBdd->NumTel}'"
			.", DateNaiss='{$this->oEnregBdd->DateNaiss}'"
			.", Adresse='".MySQLEscapeString($this->oEnregBdd->Adresse)."'"
			.", Mdp='{$this->oEnregBdd->Mdp}'"
			.($this->oEnregBdd->IdPers > 0 ? " WHERE IdPers='{$this->oEnregBdd->IdPers}'" : NULL);
		
		$this->oBdd->executerRequete($sRequeteSql);
		return TRUE;
	}
	
	/**
	 * Lie une personne a une formation dans la DB (table : formation_inscrit).
	 * 
	 * On prend l'ID de la personne correspondant au : nom + prenom + pseudo (eviter les doublons)
	 * Si il n'y a pas d'ID, on ne fait pas de lien.
	 * 
	 * @return	\c message
	 */
	function lierPersForm($v_sIdFormation=0)
	{
		$v_sMessage = NULL;

		if ($v_sIdFormation!=NULL)
		{
			$requeteIdPers = $this->oBdd->executerRequete(
				"SELECT IdPers AS IdPersonne FROM Personne "
				."WHERE Nom='".$this->retNom()."' AND Prenom='".$this->retPrenom()."' AND Pseudo='".$this->retPseudo()."'");
			$oEnreg = $this->oBdd->retEnregSuiv($requeteIdPers);
			
			if ($oEnreg->IdPersonne)
			{
			$hResult = $this->oBdd->executerRequete(
				"REPLACE INTO Formation_Inscrit SET"
				." IdForm='".$v_sIdFormation."',"
				." IdPers='".$oEnreg->IdPersonne."'");
			
			// on cherche le nom de la formation.
			$requeteNomForm = $this->oBdd->executerRequete(
				"SELECT NomForm AS NomFormation FROM Formation "
				."WHERE IdForm='".$v_sIdFormation."'");
			$oEnreg = $this->oBdd->retEnregSuiv($requeteNomForm);
				
			$v_sMessage .= "<span class=\"importOKPetit\">Cet utilisateur a &eacute;t&eacute; affect&eacute &agrave; la formation : '<strong>".$oEnreg->NomFormation."</strong>'!</span>"
						."<br /><small>Notez que ses informations personnelles (pseudo, mdp, email etc.) <ins>n'ont pas &eacute;t&eacute; modifi&eacute;es</ins> suite &agrave; cette importation.</small>";
			return $v_sMessage;
			}
			else
			{
				$v_sMessage .= "<span class=\"importAvertPetit\">Cette personne existe d&eacute;j&agrave; sur Esprit avec un autre pseudo  : ".$this->retPseudo()."!</span>";
				return $v_sMessage;
			}
		}
		return $v_sMessage;
	}
	
	/** @name Fonctions de définition des champs pour cette personne */
	//@{
	function defNom($v_sNom) { $this->oEnregBdd->Nom = mysql_real_escape_string(trim($v_sNom)); }
	function defPrenom($v_sPrenom) { $this->oEnregBdd->Prenom = mysql_real_escape_string(trim($v_sPrenom)); }
	function defPseudo($v_sPseudo) { $this->oEnregBdd->Pseudo = mysql_real_escape_string(trim($v_sPseudo)); }
	function defDateNaiss($v_sDateNaiss) { $this->oEnregBdd->DateNaiss = $v_sDateNaiss; }
	function defSexe($v_cSexe) { $this->oEnregBdd->Sexe = $v_cSexe; }
	function defEmail($v_sEmail) { $this->oEnregBdd->Email = trim($v_sEmail); }
	function defMdp($v_sMdp) { $this->oEnregBdd->Mdp = $v_sMdp; }
	function defAdresse($v_sAdresse) { $this->oEnregBdd->Adresse = mysql_real_escape_string($v_sAdresse); }
	function defNumTel($v_sNumTel) { $this->oEnregBdd->NumTel = $v_sNumTel; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cette personne */
	//@{
	function retId() { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retNom($v_bHtmlEntities = FALSE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->Nom) : $this->oEnregBdd->Nom); }
	function retPrenom($v_bHtmlEntities = FALSE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->Prenom) : $this->oEnregBdd->Prenom); }
	function retPseudo() { return $this->oEnregBdd->Pseudo; }
	function retDateNaiss()
	{
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1", $this->oEnregBdd->DateNaiss);
	}
	function retSexe()
	{
		if (isset($this->oEnregBdd->Sexe)
			&& (PERSONNE_SEXE_MASCULIN == $this->oEnregBdd->Sexe
				|| PERSONNE_SEXE_FEMININ == $this->oEnregBdd->Sexe))
			return $this->oEnregBdd->Sexe;
		
		return PERSONNE_SEXE_MASCULIN;
	}
	function retAdresse() { return emb_htmlentities($this->oEnregBdd->Adresse); }
	function retNumTel() { return $this->oEnregBdd->NumTel; }
	function retEmail() { return $this->oEnregBdd->Email; }
	function retUrlPerso() { return $this->oEnregBdd->UrlPerso; }
	function retMdp() { return $this->oEnregBdd->Mdp; }
	//@}
	
	/**
	 * Retourne la date de naissance de la personne sous forme de tableau
	 * 
	 * @return	un tableau à 3 éléments, contenant respctivement le jour (jj), le mois (mm), et l'année (aaaa), 
	 * 			représentant la date de naissance de la personne
	 */
	function retTableauDateNaiss()
	{
		$tmp = explode("-",$this->oEnregBdd->DateNaiss);
		
		$sJour  = (empty($tmp[2]) ? "01" : $tmp[2]);
		$sMois  = (empty($tmp[1]) ? "Janvier" : $tmp[1]);
		$sAnnee = (empty($tmp[0]) ? "0000" : $tmp[0]);
		
		return array("jour" => $sJour, "mois" => $sMois, "annee" => $sAnnee);
	}
	
	/**
	 * Retourne le nom complet de la personne, càd ses nom et prénom, dans un ordre choisi
	 * 
	 * @param	v_bInverser	si \c true, le nom se trouvera devant le prénom dans la chaîne retourner. Si \c false, 
	 * 			le prénom précèdera le nom
	 * 
	 * @return	une chaîne de caractère contenant les nom et prénom de la personne, dans l'odre voulu. Le nom est 
	 * 			entièrement en majuscule, alors que pour le prénom seules la première lettre de chaque mot (si prénom 
	 * 			composé) est capitalisée
	 */
	function retNomComplet($v_bInverser = FALSE)
	{
		$sNom = $this->retNom(); $sPrenom = $this->retPrenom();
		
		if (!empty($sNom) && !empty($sPrenom))
			if ($v_bInverser) return strtoupper($sNom)." ".ucwords($sPrenom);
			else return ucwords($sPrenom)." ".strtoupper($sNom);
	}
	
	/**
	 * Vérifie que la personne a le statut de tuteur dans un module indiqué
	 * 
	 * @param	v_iIdMod	l'id du module à vérifier
	 * 
	 * @return	\c true si cette personne est bien tuteur dans le module indiqué
	 */
	function verifTuteur($v_iIdMod)
	{
		$sRequeteSql = 
			 " SELECT *"
			." FROM Module_Tuteur"
			." WHERE IdPers='".$this->retId()."'"
			." AND IdMod='{$v_iIdMod}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bEstTuteur = ($this->oBdd->retEnregSuiv($hResult) ? TRUE : FALSE);
		$this->oBdd->libererResult($hResult);
		
		return $bEstTuteur;
	}
	
	/**
	 * Remplit un tableau (\c aoModules) avec les modules pour lesquels cette personne est tuteur, avec possibilité 
	 * de restreindre la recherche à une formation ou un module spécifique
	 * 
	 * @param	v_iIdMod	l'id du module unique sur lequel se fera la recherche (une valeur de retour maximum). Par 
	 * 						défaut =0, donc recherche non restreinte à un module précis
	 * @param	v_iIdForm	l'id de la formation dans laquelle se fera la recherche, donc seuls les modules de cette 
	 * 						formation seront considérés. Par défaut =0, ce qui signifie que la recherche n'est pas 
	 * 						restreinte à une formation précise
	 * 
	 * @return	le nombre de modules trouvés
	 */
	function initModules($v_iIdMod = 0, $v_iIdForm = 0)
	{
		$sRequeteSql = 
			 " SELECT m.*"
			." FROM Module AS m"
			."  LEFT JOIN Module_Tuteur AS mt ON mt.IdMod=m.IdMod"
			."  LEFT JOIN Formation AS f ON f.IdForm=m.IdForm"
			." WHERE mt.IdPers=".$this->retId()
			.($v_iIdForm > 0 ? " AND f.IdForm={$v_iIdForm}" : NULL)
			.($v_iIdMod > 0 ? " AND m.IdMod={$v_iIdMod}" : NULL);
		
		$iIndexModules = 0;
		
		$this->aoModules = array();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoModules[$iIndexModules] = new CModule($this->oBdd);
			$this->aoModules[$iIndexModules]->init($oEnreg);
			$iIndexModules++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexModules;
	}
}

?>
