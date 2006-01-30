<?php

/*
** Fichier ................: forum_prefs.tbl.php
** Description ............:
** Date de création .......: 26/11/2004
** Dernière modification ..: 21/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CForumPrefs extends CForum
{
	var $iIdForumPrefs;
	var $aoSujetsCourriel;
	
	function CForumPrefs (&$v_oBdd,$v_iIdForumPrefs=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdForumPrefs = $v_iIdForumPrefs;
		
		if (isset($this->iIdForumPrefs))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iIdForumPrefs = $this->oEnregBdd->IdForumPrefs;
		}
		else
		{
			$sRequeteSql = "SELECT Forum.*"
				.", ForumPrefs.CopieCourriel"
				.", ForumPrefs.IdPers AS IdPersForumPrefs"
				." FROM ForumPrefs"
				." LEFT JOIN Forum USING (IdForum)"
				." WHERE ForumPrefs.IdForumPrefs='".$this->retIdForumPrefs()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdForum;
	}
	
	function initForum ($v_iIdForum) { $this->iId = $v_iIdForum; parent::init(); }
	
	function initForumPrefs ($v_iIdForum,$v_iIdPers)
	{
		$this->oEnregBdd = NULL;
		
		if ($v_iIdForum < 1 || $v_iIdPers < 1)
			return;
		
		$sRequeteSql = "SELECT Forum.*"
			.", ForumPrefs.IdForumPrefs"
			.", ForumPrefs.CopieCourriel"
			.", ForumPrefs.IdPers AS IdPersForumPrefs"
			." FROM Forum"
			." LEFT JOIN ForumPrefs"
				." ON Forum.IdForum=ForumPrefs.IdForum"
				." AND ForumPrefs.IdPers='{$v_iIdPers}'"
			." WHERE Forum.IdForum='{$v_iIdForum}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		
		$this->iId = $this->oEnregBdd->IdForum;
		$this->iIdForumPrefs = $this->oEnregBdd->IdForumPrefs;
		
		return (is_numeric($this->iIdForumPrefs) && $this->iIdForumPrefs > 0);
	}
	
	function initForumsPrefs ($v_iIdForum,$v_iIdPers=NULL)
	{
		$iIdxForumPrefs = 0;
		$this->aoForumsPrefs = array();
		
		if ($v_iIdForum > 0 || $v_iIdPers > 0)
		{
			$sRequeteSql = "SELECT ForumPrefs.*"
				." FROM ForumPrefs"
				." WHERE ForumPrefs.IdForum='{$v_iIdForum}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoForumsPrefs[$iIdxForumPrefs] = new CForumPrefs($this->oBdd);
				$this->aoForumsPrefs[$iIdxForumPrefs]->init($oEnreg);
				$iIdxForumPrefs++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxForumPrefs;
	}
	
	function ajouter ($v_iIdForum,$v_iIdPers,$v_iCopieCourriel)
	{
		$sRequeteSql = "INSERT INTO ForumPrefs SET"
			." IdForumPrefs=NULL"
			.", CopieCourriel='".(int)$v_iCopieCourriel."'"
			.", IdForum='{$v_iIdForum}'"
			.", IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iIdForumPrefs = $this->oBdd->retDernierId($hResult);
		$this->init();
	}
	
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE ForumPrefs SET"
			." CopieCourriel='".$this->retCopieCourriel()."'"
			." WHERE IdForumPrefs='".$this->retIdForumPrefs()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer ()
	{
		$this->verrouillerTables();
		$this->effacerForumPrefs();
		$this->verrouillerTables(FALSE);
	}
	
	function effacerForumPrefs ()
	{
		$this->effacerEquipes();
		
		$sRequeteSql = "DELETE FROM ForumPrefs"
			." WHERE IdForumPrefs='".$this->retIdForumPrefs()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	// {{{ Liste des équipes sélectionnées pour les copies courriel
	function initEquipes ()
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		$iIdForumPrefs = $this->retIdForumPrefs();
		
		$sRequeteSql = "SELECT Equipe.*"
			.", ForumPrefs.CopieCourriel"
			." FROM ForumPrefs"
			." LEFT JOIN ForumPrefs_CopieCourrielEquipe USING (IdForumPrefs)"
			." LEFT JOIN Equipe USING (IdEquipe)"
			." WHERE ForumPrefs.IdForumPrefs='{$iIdForumPrefs}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
			$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
			$this->aoEquipes[$iIdxEquipe]->estSelectionne = ($oEnreg->CopieCourriel == '1');
			$iIdxEquipe++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxEquipe;
	}
	
	/**
	 * Cette méthode efface les enregistrements de la table
	 * "ForumPrefs_CopieCourrielEquipe" contenant les équipes qui sont attachées
	 * à la table "ForumPrefs".
	 */
	function effacerEquipes ()
	{
		$sRequeteSql = "DELETE FROM ForumPrefs_CopieCourrielEquipe"
			." WHERE IdForumPrefs='".$this->retIdForumPrefs()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Cette méthode va insérer dans la table une liste des équipes
	 * sélectionnées par l'utilisateur.
	 * @param $v_aiIdsEquipes array Liste de numéros d'identifiants des équipes
	 */
	function defEquipes ($v_aiIdsEquipes)
	{
		$sValeursRequete = NULL;
		
		if (($iIdForumPrefs = $this->retIdForumPrefs()) < 1)
			return;
		
		settype($v_aiIdsEquipes,"array");
		
		foreach ($v_aiIdsEquipes as $iIdEquipe)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$iIdForumPrefs}','{$iIdEquipe}')";
		
		$this->effacerEquipes();
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "INSERT INTO ForumPrefs_CopieCourrielEquipe"
				." (IdForumPrefs, IdEquipe)"
				." VALUES"
				.$sValeursRequete;
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	// }}}
	
	function initAuteurCopieCourriel ()
	{
		$this->oAuteurCopieCourriel = new CPersonne($this->oBdd,$this->retIdPersForumPrefs());
	}
	
	function peutEnvoyerCopieCourrielForum ()
	{
		$bPeutEnvoyerCopieCourrielForum = FALSE;
		
		$sRequeteSql = "SELECT * FROM ForumPrefs"
			." WHERE IdForum='".$this->retId()."'"
			." AND CopieCourriel='1'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregSuiv($hResult))
			$bPeutEnvoyerCopieCourrielForum = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bPeutEnvoyerCopieCourrielForum;
	}
	
	function peutEnvoyerCopieCourrielEquipe ($v_iIdEquipe)
	{
		$bPeutEnvoyerCopieCourrielEquipe = FALSE;
		
		$sRequeteSql = "SELECT * FROM ForumPrefs_CopieCourrielEquipe"
			." WHERE IdForumPrefs='".$this->retIdForumPrefs()."'"
			." AND IdEquipe='{$v_iIdEquipe}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregSuiv($hResult))
			$bPeutEnvoyerCopieCourrielEquipe = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bPeutEnvoyerCopieCourrielEquipe;
	}
	
	function peutEnvoyerCopieCourriel ($v_iIdEquipe=0)
	{
		if ($this->retCopieCourriel())
		{
			if ($this->estForumParEquipe())
				return $this->peutEnvoyerCopieCourrielEquipe($v_iIdEquipe);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	function envoyerCopieCourriel ($v_sSujet,$v_sMessage,$v_sAdresseElectronique,$v_sExpediteur=NULL,$v_iIdEquipe=NULL)
	{
		include_once(dir_code_lib("mail.class.php"));
		
		$oMail = new CMail($v_sSujet,$v_sMessage);
		$oMail->defExpediteur($v_sAdresseElectronique,$v_sExpediteur);
		$oMail->ajouterDestinataire("undisclosed-recipients:;");
		
		// Permet d'envoyer une copie cachée à l'administrateur de la plate-forme
		/*if (defined("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN") &&
			strlen(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN))
			$oMail->defCopieCarboneInvisible(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN);*/
		
		$sRequeteSql = "SELECT Personne.Nom, Personne.Prenom, Personne.Email"
			." FROM ForumPrefs"
			." LEFT JOIN Personne USING (IdPers)"
			.($v_iIdEquipe > 0 ? " LEFT JOIN ForumPrefs_CopieCourrielEquipe ON ForumPrefs.IdForumPrefs=ForumPrefs_CopieCourrielEquipe.IdForumPrefs" : NULL)
			.($v_iIdEquipe > 0 ? " LEFT JOIN Equipe USING (IdEquipe)" : NULL)
			." WHERE ForumPrefs.IdForum='".$this->retId()."'"
			." AND ForumPrefs.CopieCourriel='1'"
			.($v_iIdEquipe > 0 ? " AND ForumPrefs_CopieCourrielEquipe.IdEquipe='{$v_iIdEquipe}'" : NULL)
			." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			if (emailValide($oEnreg->Email))
				$oMail->defCopieCarboneInvisible($oEnreg->Email,$oEnreg->Nom." ".$oEnreg->Prenom);
		
		$this->oBdd->libererResult($hResult);
		
		return $oMail->envoyer();
	}
	
	function verrouillerTables ($v_bVerrouillerTables=TRUE)
	{
		// Vérrouiller les tables
		if ($v_bVerrouillerTables)
			$sRequeteSql = "LOCK TABLES ".$this->STRING_LOCK_TABLES();
		else
			$sRequeteSql = "UNLOCK TABLES";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	// {{{ Méthodes de retour
	function retIdForumPrefs () { return is_numeric($this->iIdForumPrefs) ? $this->iIdForumPrefs : 0; }
	function retCopieCourriel () { return $this->oEnregBdd->CopieCourriel; }
	function retIdPersForumPrefs () { return $this->oEnregBdd->IdPersForumPrefs; }
	// }}}
	
	// {{{ Méthodes d'entrée
	function defCopieCourriel ($v_iCopieCourriel) { $this->oEnregBdd->CopieCourriel = (int)$v_iCopieCourriel; }
	// }}}
	
	function STRING_LOCK_TABLES () { return "ForumPrefs WRITE, ForumPrefs_CopieCourrielEquipe WRITE"; }
}

?>
