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
 * @file	permission.tbl.php
 * 
 * Contient la classe de gestion des permission, en rapport avec la DB
 * 
 * @date	2002/03/20
 * 
 * @author	Filippo PORCO
 */

/**
 * Gestion des permissions, et encapsulation de la table Permission de la DB
 */
 class CPermission
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id de la permission à récupérer dans la DB
	
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $aoPermissions;			///< Tableau rempli par #initPermissions(), contenant une liste de permission
	var $aiPermisStatut;		///< Tableau rempli par #initPermissionsStatut(), contenant la liste des permissions d'un statut
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CPermission (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($v_iId > 0)
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
			$this->iId = $this->oEnregBdd->IdPermission;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Permission"
				." WHERE IdPermission='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/** @name Fonctions de lecture des champs pour cette permission */
	//@{
	function retId () { return $this->iId; }
	function retNom () { return $this->oEnregBdd->NomPermis; }
	function retDescr () { return $this->oEnregBdd->DescrPermis; }
	//@}
	
	/**
	 * Initialise un tableau contenant une liste de permission
	 * 
	 * @param	v_sFiltre	chaine comprise dans le nom de la permission
	 * 
	 * @return	le nombe de permissions insérées dans le tableau
	 */
	function initPermissions ($v_sFiltre=NULL)
	{
		$iIdxPermis = 0;
		$this->aoPermissions = array();
		
		$sRequeteSql = "SELECT * FROM Permission"
			.(empty($v_sFiltre) ? NULL : " WHERE NomPermis LIKE '%{$v_sFiltre}%'")
			." ORDER BY IdPermission ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoPermissions[$iIdxPermis] = new CPermission($this->oBdd);
			$this->aoPermissions[$iIdxPermis]->init($oEnreg);
			$iIdxPermis++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxPermis;
	}
	
	/**
	 * Initialise un tableau contenant les permissions d'un statut
	 * 
	 * @param	v_iIdStatut	l'id du statut
	 * 
	 * @return	le nombre de permissions insérées dans le tableau
	 */
	function initPermissionsStatut ($v_iIdStatut)
	{
		$this->aiPermisStatut = array();
		
		$sRequeteSql = "SELECT Permission.NomPermis"
			.", Statut_Permission.IdPermission"
			." FROM Statut_Permission"
			." LEFT JOIN Permission USING (IdPermission)"
			." WHERE Statut_Permission.IdStatut='{$v_iIdStatut}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$this->aiPermisStatut[$oEnreg->NomPermis] = $oEnreg->IdPermission;
		
		$this->oBdd->libererResult($hResult);
		
		return count($this->aiPermisStatut);
	}
	
	/**
	 * Verifie si la permission est dans le tableau initialisé par #initPermissionsStatut()
	 * 
	 * @param	v_sPermission nom de la permission
	 * 
	 * @return	\c true si la permission est trouvée
	 */
	function verifPermission ($v_sPermission) { return (is_array($this->aiPermisStatut) && isset($this->aiPermisStatut[$v_sPermission])); }
}

?>
