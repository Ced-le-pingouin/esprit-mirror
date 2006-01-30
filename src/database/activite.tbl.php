<?php

/*
** Fichier ................: activite.tbl.php
** Description ............: 
** Date de création .......: 01/06/2001
** Dernière modification ..: 21/11/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("sous_activite.tbl.php"));
require_once(dir_database("equipe.tbl.php"));

define("INTITULE_ACTIV","Groupe d'actions");

class CActiv
{
	var $oBdd;
	var $iId;
	var $b_RemettreDeOrdre;
	var $oEnregBdd;
	var $oSousActivCourante;
	var $aoSousActivs;
	var $oEquipe;
	var $aoEquipes;
	var $aoActivs;
	
	function CActiv (&$v_oBdd,$v_iIdActiv=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdActiv;
		
		if ($this->iId > 0)
			$this->init();
	}
	
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
	
	function retNumOrdreMax ()
	{
		$sRequeteSql = "SELECT MAX(OrdreActiv) FROM Activ";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumOrdreMax = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNumOrdreMax;
	}
	
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
	
	function copier ($v_iIdRubrique,$v_bRecursive=TRUE)
	{
		$iIdActiv = $this->copierActivite($v_iIdRubrique);
		
		if ($iIdActiv < 1)
			return 0;
		
		// -------------------
		// Copier le répertoire de l'activité actuelle
		// vers la nouvelle activité
		// -------------------
		$oActiv = new CActiv($this->oBdd,$iIdActiv);
		
		$sRepSrc = dir_cours($this->retId(),$this->retIdFormation());
		$sRepDst = dir_cours($iIdActiv,$oActiv->retIdFormation());
		
		copyTree($sRepSrc,$sRepDst);
		
		// Vider les répertoires contenant les fichiers
		// des collecticiels (sauf le document de base) et des chats
		$oActiv->effacerRepDocuments(FALSE,"·*\-([0-9]{4})\..*");
		$oActiv->effacerRepChats(FALSE);
		
		unset($oActiv);
		
		// -------------------
		// Copier les sous-activités
		// -------------------
		if ($v_bRecursive)
			$this->copierSousActivites($iIdActiv);
		
		return $iIdActiv;
	}
	
	function copierActivite ($v_iIdRubrique)
	{
		if ($v_iIdRubrique < 1)
			return 0;
		
		$sRequeteSql = "INSERT INTO Activ SET"
			." IdActiv=NULL"
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
			.", IdRubrique='{$v_iIdRubrique}'"
			.", IdUnite='0'"
			.", OrdreActiv='{$this->oEnregBdd->OrdreActiv}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	function copierSousActivites ($v_iIdActiv)
	{
		$this->initSousActivs();
		foreach ($this->aoSousActivs as $oSousActiv)
			$oSousActiv->copier($v_iIdActiv);
		$this->aoSousActivs = NULL;
	}
	
	function rafraichir ()
	{
		if ($this->retId() > 0)
			$this->init();
	}
	
	function retRepCours ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
	{
		return dir_cours($this->iId,$this->oEnregBdd->IdForm,$v_sFichierAInclure,$v_bCheminAbsolu);
	}
	
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
	
	function Ajouter ($v_iIdRubrique,$v_iIdUnite=0)
	{
		$iNumOrdre = $this->retNombreLignes($v_iIdRubrique)+1;
		
		$sRequeteSql = "INSERT INTO Activ SET"
			." IdActiv=NULL"
			.", NomActiv='".mysql_escape_string(INTITULE_ACTIV." sans nom")."'"
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
	
	function defRemettreDeOrdre ($v_bRemettreDeOrdre=TRUE)
	{
		$this->b_RemettreDeOrdre = $v_bRemettreDeOrdre;
	}
	
	function retRemettreDeOrdre ()
	{
		return (is_bool($this->b_RemettreDeOrdre) ? $this->b_RemettreDeOrdre : TRUE);
	}
	
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
	
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_ACTIVITE,$this->iId);
	}
	
	function effacerRepDocuments ($v_bEffacerRepertoire=TRUE,$sFiltreDocs=NULL)
	{
		include_once(dir_lib("systeme_fichiers.lib.php",TRUE));
		$sRepDocs = dir_collecticiel($this->retIdFormation(),$this->retId(),NULL,TRUE);
		vider_repertoire($sRepDocs,$sFiltreDocs);
		if ($v_bEffacerRepertoire) @unlink($sRepDocs);
	}
	
	/**
	 * Effacer le répertoire qui contient toutes les archives des chats
	 *
	 * \param $v_bEffacerRepertoire Doit-on effacer le répertoire des chats ?
	 *
	 */
	function effacerRepChats ($v_bEffacerRepertoire=TRUE)
	{
		include_once(dir_lib("systeme_fichiers.lib.php",TRUE));
		$sRepChats = dir_chat_log($this->retId(),$this->retIdFormation(),NULL,TRUE);
		vider_repertoire($sRepChats);
		if ($v_bEffacerRepertoire) @unlink($sRepChats);
	}
	
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
	
	function retNbrEquipes ()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Equipe"
			." WHERE Equipe.IdActiv='$this->iId'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iNbrEquipes = $this->oBdd->retEnregPrecis($hResult);
		
		$this->oBdd->libererResult($hResult);
		
		return $iNbrEquipes;
	}
	
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
	
	function initEquipes ($v_bInitMembres=FALSE,$iDernierNiveau=TYPE_FORMATION)
	{
		$oListeEquipes = new CEquipe($this->oBdd);
		$oListeEquipes->initEquipesEx($this->retId(),TYPE_ACTIVITE,$v_bInitMembres);
		$this->aoEquipes = $oListeEquipes->aoEquipes;
		return count($this->aoEquipes);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retDateDeb () { return $this->oEnregBdd->DateDebActiv; }
	function retDateFin () { return $this->oEnregBdd->DateFinActiv; }
	
	function retModalite () { return $this->oEnregBdd->ModaliteActiv; }
	function defModalite ($v_iModalite) { $this->mettre_a_jour("ModaliteActiv",$v_iModalite); }
	
	function retTypeNiveau () { return TYPE_ACTIVITE; }
	
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutActiv",$v_iStatut);
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

	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdActiv=0)
	{
		if ($v_iIdActiv < 1)
			$v_iIdActiv = $this->retId();

		if ($v_iIdActiv < 1)
			return FALSE;

		$sRequeteSql = "UPDATE Activ SET"
			." {$v_sNomChamp}='".mysql_escape_string($v_mValeurChamp)."'"
			." WHERE IdActiv='{$v_iIdActiv}'";

		$this->oBdd->executerRequete ($sRequeteSql);
		
		return TRUE;
	}

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

	function retTypeTransfert ($v_iIdActivDst)
	{
		$iTransfert_II = 5;
		$iTransfert_IE = 9;
		$iTransfert_EE = 10;
		$iTransfert_EI = 6;
		
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

	function defNumOrdre ($v_iOrdre)
	{
		if (is_numeric($v_iOrdre))
			$this->mettre_a_jour("OrdreActiv",$v_iOrdre);
	}

	function retNumOrdre ()
	{
		return $this->oEnregBdd->OrdreActiv;
	}

	function retAfficherModalite ()
	{
		return $this->oEnregBdd->AfficherModaliteActiv;
	}

	function defAfficherModalite ($v_bAfficher)
	{
		if (is_bool($v_bAfficher))
			$this->mettre_a_jour("AfficherModaliteActiv",$v_bAfficher);
	}

	// --------------------------
	// Statut
	// --------------------------

	function retAfficherStatut ()
	{
		return $this->oEnregBdd->AfficherStatutActiv;
	}

	function defAfficherStatut ($v_bAfficher)
	{
		if (is_bool($v_bAfficher))
			$this->mettre_a_jour("AfficherStatutActiv",$v_bAfficher);
	}

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

	// --------------------------
	// Nom
	// --------------------------
	
	function defNom ($v_sNomActiv)
	{
		$v_sNomActiv = trim(stripslashes($v_sNomActiv));
		
		if (empty($v_sNomActiv))
			$v_sNomActiv = INTITULE_ACTIV." sans nom";
		
		$this->mettre_a_jour("NomActiv",$v_sNomActiv);
	}
	
	function retNom ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->NomActiv) : $this->oEnregBdd->NomActiv);
	}
	
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
	
	// --------------------------
	// Description
	// --------------------------
	
	function defDescr ($v_sDescrActiv)
	{
		$this->mettre_a_jour("DescrActiv",trim(stripslashes($v_sDescrActiv)));
	}
	
	function retDescr ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->DescrActiv) : $this->oEnregBdd->DescrActiv);
	}
	
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeActivs()) < 0)
			return 0;
		$cpt--;
		return ($cpt < 0 ?  0 : $this->aoActivs[$cpt]->IdActiv);
	}
	
	function retTypes ()
	{
		return array(array(0,INTITULE_ACTIV));
	}
	
	function retListeModalites ()
	{
		return array(
			array(MODALITE_INDIVIDUEL,"individuel"),
			array(MODALITE_PAR_EQUIPE,"par &eacute;quipe"));
	}
}

?>
