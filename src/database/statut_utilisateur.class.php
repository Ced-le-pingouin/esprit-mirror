<?php

/*
** Fichier ................: statut_utilisateur.class.php
** Description ............: 
** Date de cr�ation .......: 20/02/2003
** Derni�re modification ..: 29/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
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
		
		// {{{ Est-t-il �tudiant ?
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
		
		// {{{ Visiteur par d�faut
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
	
	function retSuperieurStatut ($v_iDepartStatutUtiliteur=STATUT_PERS_PREMIER)
	{
		for ($iIdxStatut=$v_iDepartStatutUtiliteur; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
			if ($this->aiStatuts[$iIdxStatut])
				break;
		return $iIdxStatut;
	}
	
	function retInferieurStatut ()
	{
		for ($iIdxStatut=STATUT_PERS_DERNIER; $iIdxStatut>STATUT_PERS_PREMIER; $iIdxStatut--)
			if ($this->aiStatuts[$iIdxStatut])
				break;
		return $iIdxStatut;
	}
	
	function retNbrStatuts ()
	{
		$iNbrStatuts = 0;
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
			if ($this->aiStatuts[$iIdxStatut])
				$iNbrStatuts++;
		
		return $iNbrStatuts;
	}
	
	function verifStatut ($v_iIdStatut)
	{
		if (is_array($this->aiStatuts) && isset($this->aiStatuts[$v_iIdStatut]))
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
