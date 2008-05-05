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
 * @file	statut_permission.tbl.php
 * 
 * Contient la classe de gestion des permission d'un statut, en rapport avec la DB
 * 
 * @date	2005/03/18
 * 
 * @author	Filippo PORCO
 */

/**
 * Gestion des permissions d'un statut, et encapsulation de la table Statut_Permission de la DB
 */
class CStatutPermission
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $abPermissions;		///< Tableau rempli par #initPermissions(), contenant une liste des permissions d'un statut
	
	/**
	 * Constructeur.
	 * 
	 * @param	v_oBdd	l'objet CBdd qui représente la connexion courante à la DB
	 */
	function CStatutPermission (&$v_oBdd)
	{
		$this->oBdd = &$v_oBdd;
	}
	
	/**
	 * Initialise un tableau contenant une liste de permissions par rapport au statut fourni en paramètre
	 * 
	 * @param	v_iIdStatut	l'id du statut
	 * 
	 * @return	le nombre de permissions insérées dans le tableau
	 */
	function initPermissions ($v_iIdStatut)
	{
		$iIdxPermis = 0;
		$this->abPermissions = array();
		
		$sRequeteSql = "SELECT Statut_Permission.*"
			.", Permission.NomPermis"
			." FROM Statut_Permission"
			." LEFT JOIN Permission USING (IdPermission)"
			." WHERE Statut_Permission.IdStatut='{$v_iIdStatut}'"
			." ORDER BY Statut_Permission.IdPermission ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->abPermissions[$oEnreg->NomPermis] = TRUE;
			$iIdxPermis++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxPermis;
	}
	
	/**
	 * Vérifie si le statut a cette permission
	 * 
	 * @param	v_sNomPermission le nom de la permission
	 * 
	 * @return	\c true si le statut a cette permission
	 */
	function verifPermission ($v_sNomPermission) { return is_array($this->abPermissions) && isset($this->abPermissions[$v_sNomPermission]); }
	
	/**
	 * Ajoute une permission à un statut
	 * 
	 * @param	v_iIdPermis	l'id de la permission
	 * @param	v_iIdStatut	l'id du statut
	 */
	function ajouter ($v_iIdPermis,$v_iIdStatut)
	{
		$sRequeteSql = "REPLACE INTO Statut_Permission"
			." (IdPermission,IdStatut) VALUES ('{$v_iIdPermis}','{$v_iIdStatut}')";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface une permission à un statut
	 * 
	 * @param	v_iIdPermis	l'id de la permission
	 * @param	v_iIdStatut	l'id du statut
	 */
	function effacer ($v_iIdPermis,$v_iIdStatut)
	{
		$sRequeteSql = "DELETE FROM Statut_Permission"
			." WHERE IdPermission='{$v_iIdPermis}'"
			." AND IdStatut='{$v_iIdStatut}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Optimise la table Statut_Permission
	 * 
	 */
	function optimiser () { $this->oBdd->executerRequete("OPTIMIZE TABLE Statut_Permission"); }
}
?>
