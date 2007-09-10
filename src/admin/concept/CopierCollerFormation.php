<?php
require_once 'globals.inc.php';
require_once 'admin_globals.inc.php';
require_once dir_include('template.inc.php', TRUE);
require_once dir_include('IterateurElementFormation.php', TRUE);
require_once dir_lib('std/AfficheurPage.php', TRUE);
require_once dir_lib('std/PressePapiers.php', TRUE);
require_once dir_include('ElementFormation.php', TRUE);
require_once dir_database('formation.tbl.php', TRUE);

class CopierCollerFormation extends AfficheurPage
{
	var $oProjet;
	
	var $aoFormationsSrc;
	var $aoFormationsDest;
	
	var $iIdFormationSrc;
	var $iIdFormationDest;
	var $oFormationSrc;
	var $oFormationDest;
	var $aBranchesSrcSel;
	var $brancheDestSel;
	
	var $oPressePapiers;
	var $elemPpSel;
	
	var $iTypeElemAColler;
	var $iIdElemAColler;
	var $iTypeElemDest;
	var $iIdElemDest;
	
	function recupererDonnees()
	{
		$sActionsReconnues = array('changerFormation', 'copier', 'coller', 'supprimerElemPp', 'viderPp');
		$this->sAction = array_shift(array_intersect($sActionsReconnues, array_keys($this->aDonneesForm)));
		
		$this->oProjet = new CProjet();
		
		$this->iIdFormationSrc = $this->aDonneesForm['idFormationSrc'] ? 
		                         $this->aDonneesForm['idFormationSrc'] : $this->aDonneesUrl['idFormationSrc'];
		                         
		$this->iIdFormationDest = $this->aDonneesForm['idFormationDest'] ? 
		                          $this->aDonneesForm['idFormationDest'] : $this->aDonneesUrl['idFormationDest'];
		
		$this->aBranchesSrcSel = !empty($this->aDonneesForm['branchesSrcSel']) ? 
		                         $this->aDonneesForm['branchesSrcSel'] : array();
		$this->brancheDestSel = $this->aDonneesForm['brancheDestSel'];
		
		$this->oPressePapiers =& $this->aDonneesPersist['pressePapiersFormation'];
		if (!isset($this->oPressePapiers))
			$this->oPressePapiers = new PressePapiers();
		
		$this->elemPpSel = $this->aDonneesForm['elemPpSel'];
			
		$this->oProjet->initFormations();
		$this->aoFormationsSrc = $this->oProjet->aoFormations;
		$this->oProjet->initFormationsUtilisateur();
		$this->aoFormationsDest = $this->oProjet->aoFormations;
	}
	
	function validerDonnees()
	{
		if (!empty($this->iIdFormationSrc))
			$this->oFormationSrc = new CFormation($this->oProjet->oBdd, $this->iIdFormationSrc);
		else if (count($this->aoFormationsSrc) > 0)
			$this->oFormationSrc = $this->aoFormationsSrc[0];
		
		if (!$this->oFormationSrc || !$this->oFormationSrc->oEnregBdd)
			$this->declarerErreur('erreurFormationSrc', TRUE,
                                  "Aucune formation n'est disponible comme source de la copie");
		
		if (!empty($this->iIdFormationDest))
			$this->oFormationDest = new CFormation($this->oProjet->oBdd, $this->iIdFormationDest);
		else
			$this->oFormationDest = $this->oProjet->oFormationCourante;
		
		if (!$this->oFormationDest || !$this->oFormationDest->oEnregBdd)
			$this->declarerErreur('erreurFormationDest', TRUE,
                                  "Aucune formation n'est disponible comme cible de la copie");
		
		//// permission formations src/dest ???

		if ($this->sAction == 'copier' && !count($this->aBranchesSrcSel))
			$this->declarerErreurAction('erreurBranchesSrc', FALSE, 'Aucun élément source sélectionné pour la copie');
		if ($this->sAction == 'coller')
		{
			if (empty($this->elemPpSel))
			{
				$this->declarerErreurAction('erreurElemPpCopie', FALSE, 
				                            "Aucun élément source (presse-papiers) sélectionné pour coller");
			}
			else if (empty($this->brancheDestSel))
			{
				$this->declarerErreurAction('erreurBrancheDest', FALSE,
			    	                        "Aucun emplacement cible sélectionné pour coller");
			}
			else
			{
				list($this->iTypeElemAColler, $this->iIdElemAColler) = explode('_', $this->elemPpSel);
				list($this->iTypeElemDest, $this->iIdElemDest) = explode('_', $this->brancheDestSel);
				if (!ElementFormation::typeEstFrereDe($this->iTypeElemAColler, $this->iTypeElemDest)
				 && !ElementFormation::typeEstEnfantDe($this->iTypeElemAColler, $this->iTypeElemDest))
					$this->declarerErreurAction('erreurDestCopie', FALSE,
					                            'La branche copiée doit être de même niveau ou de niveau juste '
					                           .'inférieur à la branche de destination de la copie');
			}
		}
					
		if ($this->sAction == 'supprimerElemPp' && empty($this->elemPpSel))
			$this->declarerErreurAction('erreurElemPpSuppr', FALSE, 'Aucun élément sélectionné pour la suppression');
	}
	
	function gererActions()
	{
		switch($this->sAction)
		{
			case 'copier':
				$bFormation = FALSE;
				foreach ($this->aBranchesSrcSel as $elem)
				{
					list($iTypeBranche,) = explode('_', $elem);
					if ($iTypeBranche > TYPE_FORMATION)
						$this->oPressePapiers->ajouterElement(new PressePapiersElement($elem, 'copier'));
				}
				break;
			
			case 'coller':
				$oElemAColler = ElementFormation::retElementFormation($this->oProjet->oBdd, $this->iTypeElemAColler, 
				                                                      $this->iIdElemAColler);
				$oElemDest = ElementFormation::retElementFormation($this->oProjet->oBdd, $this->iTypeElemDest, 
				                                                   $this->iIdElemDest);
				if (ElementFormation::typeEstFrereDe($this->iTypeElemAColler, $this->iTypeElemDest))
				{
					$iNumOrdre = $oElemDest->retNumOrdre();
					$this->iIdElemDest = $oElemDest->retIdParent();
				}
				else
				{
					$iNumOrdre = 0;
				}
				$oElemAColler->copierAvecNumOrdre($this->iIdElemDest, $iNumOrdre);
				break;
			
			case 'supprimerElemPp':
				$this->oPressePapiers->enleverElement(new PressePapiersElement($this->elemPpSel, 'copier'));
				break;
				
			case 'viderPp':
				$this->oPressePapiers->vider();
				break;
		}
	}
	
	function afficherParties()
	{
		$this->afficherFormationsSrcEtDest();
		$this->afficherPressePapiers();
	}
	
	function afficherFormationsSrcEtDest()
	{
		$aDonnees = array(array('Src', &$this->aoFormationsSrc, &$this->oFormationSrc),
		                  array('Dest', &$this->aoFormationsDest, &$this->oFormationDest)
		                 );
		
		foreach ($aDonnees as $donnees)
		{
			$tplListeFormations = new TPL_Block("form{$donnees[0]}_liste", $this->tpl);
			$tplListeFormations->beginLoop();
			for ($i = 0; $i < count($donnees[1]); $i++)
			{
				$tplListeFormations->nextLoop();
				$tplListeFormations->remplacer('{formation.id}', $donnees[1][$i]->retId());
				$tplListeFormations->remplacer('{formation.titre}', $donnees[1][$i]->retNom());
				if ($donnees[1][$i]->retId() == $donnees[2]->retId())
					$tplListeFormations->remplacer('<option ', '<option selected="selected" ');
			}
			$tplListeFormations->afficher();
			
			$tplBranche = new TPL_Block("form{$donnees[0]}_branche", $this->tpl);
			$tplBranche->beginLoop();
			
			$itrFormation = new IterateurRecursif(new IterateurElementFormation($donnees[2]),
			                                      ITR_REC_PARENT_AVANT);
			for ($bFormation = TRUE; $itrFormation->estValide(); $itrFormation->suiv())
			{
				$tplBranche->nextLoop();
				if (!$bFormation)
				{
					$branche = $itrFormation->courant();
				}
				else
				{
					$branche = $donnees[2];
					if ($donnees[0] == 'Src')
						$tplBranche->remplacer('<input ', '<input disabled="disabled" ');
					$bFormation = FALSE;
				}
				$idCompose = $branche->retTypeNiveau().'_'.$branche->retId();
				$tplBranche->remplacer('{branche.niv}', $branche->retTexteNiveau());
				$tplBranche->remplacer('{branche.type}', $branche->retTypeNiveau() == TYPE_SOUS_ACTIVITE ? 
				                                         $branche->retTexteType : '');
				$tplBranche->remplacer('{branche.id}', $donnees[0].'_'.$idCompose);
				$tplBranche->remplacer('{branche.val}', $idCompose);
				$tplBranche->remplacer('{branche.titre}', $branche->retNom());
				
				if ($donnees[0] == 'Dest' && $idCompose == $this->brancheDestSel)
					$tplBranche->remplacer('<input ', '<input checked="checked"');
			}
			
			$tplBranche->afficher();
		}
	}
	
	function afficherPressePapiers()
	{
		$itrPressePapiers = $this->oPressePapiers->retIterateur();
		$tplPressePapiers = new TPL_Block('pp_element', $this->tpl);
		$tplPressePapiers->beginLoop();
		for (; $itrPressePapiers->estValide(); $itrPressePapiers->suiv())
		{
			$elemPp = $itrPressePapiers->courant();
			$idCompose = $elemPp->retSujet();
			list($iTypeElem, $iIdElem) = explode('_', $idCompose);
			$elem = ElementFormation::retElementFormation($this->oProjet->oBdd, $iTypeElem, $iIdElem);

			$tplPressePapiers->nextLoop();
			$tplPressePapiers->remplacer('{pp.niv}', $elem->retTexteNiveau());
			$tplPressePapiers->remplacer('{pp.type}', $elem->retTypeNiveau() == TYPE_SOUS_ACTIVITE ? 
			                                          $elem->retTexteType : '');
			$tplPressePapiers->remplacer('{pp.id}', 'Pp_'.$idCompose);
			$tplPressePapiers->remplacer('{pp.val}', $idCompose);
			$tplPressePapiers->remplacer('{pp.titre}', $elem->retNom());
			
			if ($idCompose == $this->elemPpSel)
				$tplPressePapiers->remplacer('<input ', '<input checked="checked"');
		}
		$tplPressePapiers->afficher();
	}
}

$page = new CopierCollerFormation();
$page->demarrer();

//// replacer la page en copier/coller correctement
//// vérif les outils en PHP 4 avec grosses formations
//// vérif si idFormation(Src/Dest) est bien lisible/modifiable par l'utilisateur
//// faire disparaître les boutons Choisir si JS activé (submit auto)
//// vérif si GererFichiersFormation.php fonctionne encore après passage de declarerErreurAction() dans AfficheurPage
?>