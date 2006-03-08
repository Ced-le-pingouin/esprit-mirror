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
** Fichier ................: personne.tbl.php
** Description ............:
** Date de création .......: 06/09/2001
** Dernière modification ..: 13/10/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

define("PERSONNE_SEXE_FEMININ","F");
define("PERSONNE_SEXE_MASCULIN","M");

/**
 * Cette classe contient les renseignements d'une personne.
 *
 * @class CPersonne
 *
 */
class CPersonne
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $aoPersonnes;
	var $aoModules;
	
	function CPersonne (&$v_oBdd,$v_iId=0)
	{
		$this->oBdd = &$v_oBdd;
		
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdPers;
		}
		else
		{
			$sRequeteSql = "SELECT *, UPPER(Nom) AS Nom"
				." FROM Personne"
				." WHERE IdPers='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initPersonne ($v_asInfosPers)
	{
		$iInitPersonne = 0;
		
		$sConditionsRequete = NULL;
		
		foreach ($v_asInfosPers as $sCle => $sValeur)
			if (isset($sValeur))
				$sConditionsRequete .= (isset($sConditionsRequete) ? " AND " : NULL)
					."{$sCle} = '{$sValeur}'";
		
		if (isset($sConditionsRequete))
		{
			$sRequeteSql = "SELECT * FROM Personne"
				." WHERE {$sConditionsRequete}"
				." LIMIT 2";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if (($iInitPersonne = $this->oBdd->retNbEnregsDsResult($hResult)) == 1)
				$this->init($this->oBdd->retEnregSuiv($hResult));
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iInitPersonne;
	}
	
	function ajouter ()
	{
		$sRequeteSql = "INSERT INTO Personne SET IdPers=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	function retLienEmail ($v_bComplet=FALSE,$v_sSujet=NULL,$v_pseudo=FALSE)
	{
		if ($v_bComplet)
			$sNom = $this->retNomComplet();
		else
			$sNom = $this->retNom();
		
		if ($v_pseudo)
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
	
	function defNom ($v_sNom) { $this->oEnregBdd->Nom = trim($v_sNom); }
	function defPrenom ($v_sPrenom) { $this->oEnregBdd->Prenom = trim($v_sPrenom); }
	function defPseudo ($v_sPseudo) { $this->oEnregBdd->Pseudo = trim($v_sPseudo); }
	function defDateNaiss ($v_sDateNaiss) { $this->oEnregBdd->DateNaiss = $v_sDateNaiss; }
	function defSexe ($v_cSexe) { $this->oEnregBdd->Sexe = $v_cSexe; }
	function defEmail ($v_sEmail) { $this->oEnregBdd->Email = trim($v_sEmail); }
	function defMdp ($v_sMdp) { $this->oEnregBdd->Mdp = $v_sMdp; }
	function defAdresse ($v_sAdresse) { $this->oEnregBdd->Adresse = $v_sAdresse; }
	function defNumTel ($v_sNumTel) { $this->oEnregBdd->NumTel = $v_sNumTel; }
	
	function estUnique ()
	{
		$bEstUnique = TRUE;
		
		$sRequeteSql = "SELECT IdPers FROM Personne"
			." WHERE Nom = '{$this->oEnregBdd->Nom}'"
			." AND Prenom = '{$this->oEnregBdd->Prenom}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$bEstUnique = ($oEnreg->IdPers == $this->oEnregBdd->IdPers);
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstUnique;
	}
	
	function estPseudoUnique ()
	{
		$bEstUnique = TRUE;
		
		// Vérifier seulement dans le cas d'un ajout
		// d'un nouvel utilisateur
		$sRequeteSql = "SELECT IdPers FROM Personne"
			." WHERE Pseudo='{$this->oEnregBdd->Pseudo}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$bEstUnique = ($oEnreg->IdPers == $this->oEnregBdd->IdPers);
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstUnique;
	}
	
	function enregistrer ()
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
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->Nom) : $this->oEnregBdd->Nom); }
	function retPrenom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->Prenom) : $this->oEnregBdd->Prenom); }
	function retPseudo () { return $this->oEnregBdd->Pseudo; }
	
	function retTableauDateNaiss ()
	{
		$tmp = explode("-",$this->oEnregBdd->DateNaiss);
		
		$sJour  = (empty($tmp[2]) ? "00" : $tmp[2]);
		$sMois  = (empty($tmp[1]) ? "00" : $tmp[1]);
		$sAnnee = (empty($tmp[0]) ? "0000" : $tmp[0]);
		
		return array("jour" => $sJour, "mois" => $sMois, "annee" => $sAnnee);
	}
	
	function retDateNaiss ()
	{
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$this->oEnregBdd->DateNaiss);
	}
	
	function retNomComplet ($v_bInverser=FALSE)
	{
		$sNom = $this->retNom(); $sPrenom = $this->retPrenom();
		
		if (!empty($sNom) && !empty($sPrenom))
			if ($v_bInverser) return strtoupper($sNom)." ".ucwords($sPrenom);
			else return ucwords($sPrenom)." ".strtoupper($sNom);
	}
	
	function retSexe ()
	{
		if (isset($this->oEnregBdd->Sexe)
			&& (PERSONNE_SEXE_MASCULIN == $this->oEnregBdd->Sexe
				|| PERSONNE_SEXE_FEMININ == $this->oEnregBdd->Sexe))
			return $this->oEnregBdd->Sexe;
		
		return PERSONNE_SEXE_MASCULIN;
	}
	
	function retAdresse () { return htmlentities($this->oEnregBdd->Adresse); }
	function retNumTel () { return $this->oEnregBdd->NumTel; }
	function retEmail () { return $this->oEnregBdd->Email; }
	function retUrlPerso () { return $this->oEnregBdd->UrlPerso; }
	function retMdp () { return $this->oEnregBdd->Mdp; }
	
	function verifTuteur ($v_iIdMod)
	{
		$sRequeteSql = "SELECT *"
			." FROM Module_Tuteur"
			." WHERE IdPers='".$this->retId()."'"
			." AND IdMod='{$v_iIdMod}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bEstTuteur = ($this->oBdd->retEnregSuiv($hResult) ? TRUE : FALSE);
		$this->oBdd->libererResult($hResult);
		return $bEstTuteur;
	}
	
	function initModules ($v_iIdMod=0,$v_iIdForm=0)
	{
		$sRequeteSql = "SELECT m.* FROM Module AS m"
			." LEFT JOIN Module_Tuteur AS mt ON mt.IdMod=m.IdMod"
			." LEFT JOIN Formation AS f ON f.IdForm=m.IdForm"
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
