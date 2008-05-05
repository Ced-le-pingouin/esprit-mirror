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
** Fichier ................: statut_utilisateur.class.php
** Description ............: 
** Date de création .......: 20/02/2003
** Dernière modification ..: 29/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_definition("statut.def.php"));

class CStatutUtilisateur
{
	var $oBdd;
	var $iIdPers;
	
	var $aiStatuts;
	
	function CStatutUtilisateur (&$v_oBdd,$v_iIdPers=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdPers = $v_iIdPers;
	}
	
	function initStatuts ($v_iIdForm=0,$v_iIdMod=0,$v_bInscritAutoModules=TRUE)
	{
		if ($this->iIdPers > 0)
			$this->initStatutsInscrit($v_iIdForm,$v_iIdMod,$v_bInscritAutoModules);
		else
			$this->initStatutsVisiteur();
	}
	
	function initStatutsInscrit ($v_iIdForm,$v_iIdMod,$v_bInscritAutoModules)
	{
		$this->aiStatuts = array();
		
		// {{{ Est-t-il un administrateur ?
		$sRequeteSql = "SELECT COUNT(IdPers)"
			." FROM Projet_Admin"
			." WHERE IdPers='{$this->iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_ADMIN] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un responsable potentiel ?
		$sRequeteSql = "SELECT COUNT(IdPers)"
			." FROM Projet_Resp"
			." WHERE IdPers='{$this->iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_RESPONSABLE_POTENTIEL] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un responsable de formation ?
		$sRequeteSql = "SELECT COUNT(Formation_Resp.IdPers)"
			." FROM Formation_Resp"
			." LEFT JOIN Formation USING (IdForm)"
			." WHERE Formation_Resp.IdPers='{$this->iIdPers}'"
			.($v_iIdForm > 0 ? " AND Formation_Resp.IdForm='{$v_iIdForm}'" : NULL)
			." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_RESPONSABLE] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un concepteur potentiel ?
		$sRequeteSql = "SELECT COUNT(IdPers)"
			." FROM Projet_Concepteur"
			." WHERE IdPers='{$this->iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_CONCEPTEUR_POTENTIEL] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un concepteur de module ?
		if ($v_iIdMod > 0)
			$sRequeteSql = "SELECT COUNT(Module_Concepteur.IdPers)"
				." FROM Module_Concepteur"
				." LEFT JOIN Module USING (IdMod)"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Module_Concepteur.IdPers='{$this->iIdPers}'"
				." AND Module_Concepteur.IdMod='{$v_iIdMod}'"
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		else
			$sRequeteSql = "SELECT COUNT(Formation_Concepteur.IdPers)"
				." FROM Formation_Concepteur"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Formation_Concepteur.IdPers='{$this->iIdPers}'"
				.($v_iIdForm > 0 ? " AND Formation_Concepteur.IdForm='{$v_iIdForm}'" : NULL)
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_CONCEPTEUR] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un chercheur ?
		$this->aiStatuts[STATUT_PERS_CHERCHEUR] = FALSE;
		// }}}
		
		// {{{ Est-t-il tuteur ?
		if ($v_iIdMod > 0)
			$sRequeteSql = "SELECT COUNT(Module_Tuteur.IdPers)"
				." FROM Module_Tuteur"
				." LEFT JOIN Module USING (IdMod)"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Module_Tuteur.IdPers='{$this->iIdPers}'"
				." AND Module_Tuteur.IdMod='{$v_iIdMod}'"
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		else
			$sRequeteSql = "SELECT COUNT(Formation_Tuteur.IdPers)"
				." FROM Formation_Tuteur"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Formation_Tuteur.IdPers='{$this->iIdPers}'"
				.($v_iIdForm > 0 ? " AND Formation_Tuteur.IdForm='{$v_iIdForm}'" : NULL)
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_TUTEUR] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Est-t-il un co-tuteur ?
		$this->aiStatuts[STATUT_PERS_COTUTEUR] = FALSE;
		// }}}
		
		// {{{ Est-t-il étudiant ?
		if ($v_iIdMod > 0 && !$v_bInscritAutoModules)
			$sRequeteSql = "SELECT COUNT(Module_Inscrit.IdPers)"
				." FROM Module_Inscrit"
				." LEFT JOIN Module USING (IdMod)"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Module_Inscrit.IdPers='{$this->iIdPers}'"
				." AND Module_Inscrit.IdMod='{$v_iIdMod}'"
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		else
			$sRequeteSql = "SELECT COUNT(Formation_Inscrit.IdPers)"
				." FROM Formation_Inscrit"
				." LEFT JOIN Formation USING (IdForm)"
				." WHERE Formation_Inscrit.IdPers='{$this->iIdPers}'"
				.($v_iIdForm > 0 ? " AND Formation_Inscrit.IdForm='{$v_iIdForm}'" : NULL)
				." AND Formation.StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->aiStatuts[STATUT_PERS_ETUDIANT] = ($this->oBdd->retEnregPrecis($hResult) > 0);
		$this->oBdd->libererResult($hResult);
		// }}}
		
		// {{{ Visiteur par défaut
		$this->aiStatuts[STATUT_PERS_VISITEUR] = TRUE;
		// }}}
	}
	
	function initStatutsVisiteur ()
	{
		$this->aiStatuts = array();
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
			$this->aiStatuts[$iIdxStatut] = FALSE;
		
		$this->aiStatuts[STATUT_PERS_VISITEUR] = TRUE;
	}
	
	function retSuperieurStatut ($v_iDepartStatutUtilisateur=STATUT_PERS_PREMIER)
	{
		for ($iIdxStatut=$v_iDepartStatutUtilisateur; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
			if (isset($this->aiStatuts[$iIdxStatut]) && $this->aiStatuts[$iIdxStatut])
				break;
		return $iIdxStatut;
	}
	
	function retInferieurStatut ()
	{
		for ($iIdxStatut=STATUT_PERS_DERNIER; $iIdxStatut>STATUT_PERS_PREMIER; $iIdxStatut--)
			if (isset($this->aiStatuts[$iIdxStatut]) && $this->aiStatuts[$iIdxStatut])
				break;
		return $iIdxStatut;
	}
	
	function retNbrStatuts ()
	{
		$iNbrStatuts = 0;
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
			if (isset($this->aiStatuts[$iIdxStatut]) && $this->aiStatuts[$iIdxStatut])
				$iNbrStatuts++;
		
		return $iNbrStatuts;
	}
	
	function verifStatut ($v_iIdStatut)
	{
		if (isset($this->aiStatuts[$v_iIdStatut]))
			return $this->aiStatuts[$v_iIdStatut];
		else
			return FALSE;
	}
	
	function estAdministrateur () { return $this->verifStatut(STATUT_PERS_ADMIN); }
	function estResponsablePotentiel () { return $this->verifStatut(STATUT_PERS_RESPONSABLE_POTENTIEL); }
	function estResponsable () { return $this->verifStatut(STATUT_PERS_RESPONSABLE); }
	function estConcepteurPotentiel () { return $this->verifStatut(STATUT_PERS_CONCEPTEUR_POTENTIEL); }
	function estConcepteur () { return $this->verifStatut(STATUT_PERS_CONCEPTEUR); }
	function estTuteur () { return $this->verifStatut(STATUT_PERS_TUTEUR); }
	function estEtudiant () { return $this->verifStatut(STATUT_PERS_ETUDIANT); }
}

?>
