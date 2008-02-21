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
 * @file	CopierCollerFormation.php
 */

require_once 'globals.inc.php';
require_once 'admin_globals.inc.php';
require_once dir_include('template.inc.php', TRUE);
require_once dir_include('IterateurElementFormation.php', TRUE);
require_once dir_lib('std/AfficheurPage.php', TRUE);
require_once dir_lib('std/PressePapiers.php', TRUE);
require_once dir_include('ElementFormation.php', TRUE);
require_once dir_database('formation.tbl.php', TRUE);

/**
 * Contrôleur pour l'outil de copier/coller de (bouts de) formations
 */
class CopierCollerFormation extends AfficheurPage
{
	var $oProjet;			///< Accès aux méthodes et données du projet global
	
	var $aoFormationsSrc;	///< Liste des formations source accessibles pour l'action Copier
	var $aoFormationsDest;  ///< Liste des formations cible accessibles pour l'action Coller
	
	var $iIdFormationSrc;	///< Id de la formation source sélectionnée, à afficher dans le panneau Copier
	var $iIdFormationDest;	///< Id de la formation cible sélectionnée, à afficher dans le panneau Coller
	var $oFormationSrc;		///< Objet qui contient la formation source affichée
	var $oFormationDest;	///< Objet qui contient la formation cible affichée
	var $aBranchesSrcSel;	///< Branches (modules, rubriques, etc.) sélectionnées dans le panneau Copier
	var $brancheDestSel;	///< Branche cible sélectionnée dans le panneau Coller
	
	var $oPressePapiers;	///< Objet qui représente le presse-papiers (contient des modules, rubriques, etc.)
	var $elemPpSel;			///< Elément sélectionné dans le panneau Presse-papiers
	
	var $iTypeElemAColler;	///< Type (niveau) de l'élément source de l'opération Coller
	var $iIdElemAColler;	///< Id de l'élément source de l'opération Coller
	var $iTypeElemDest;		///< Type (niveau) de l'élément de destination de l'opération Coller
	var $iIdElemDest;		///< Id de l'élément de destination de l'opération Coller
	
	var $sOngletCourant;	///< Utile seulement si Javascript est activé, retient l'onglet (Copier ou Coller) actif
	
	/**
	 * @see	AfficheurPage#recupererDonnees()
	 */
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
		
		// presse-papiers = variable de session
		$this->oPressePapiers =& $this->aDonneesPersist['pressePapiersFormation'];
		if (!isset($this->oPressePapiers))
			$this->oPressePapiers = new PressePapiers();
		
		$this->elemPpSel = $this->aDonneesForm['elemPpSel'];
		
		$this->sOngletCourant = !empty($this->aDonneesForm['ongletCourant']) ?
		                        $this->aDonneesForm['ongletCourant'] : '';
		
		// init listes des formations pour onglets Copier et Coller
		$this->oProjet->initFormations(NULL, FALSE);
		$this->aoFormationsSrc = $this->oProjet->aoFormations;
		$this->oProjet->initFormationsUtilisateur(FALSE, TRUE, FALSE, FALSE);
		$this->aoFormationsDest = $this->oProjet->aoFormations;
	}
	
	/**
	 * @see AfficheurPage#validerDonnees()
	 */
	function validerDonnees()
	{
		// sélectionner la formation source: choisie, ou la 1ère de la liste
		if (!empty($this->iIdFormationSrc))
			$this->oFormationSrc = new CFormation($this->oProjet->oBdd, $this->iIdFormationSrc);
		else if (!empty($this->oProjet->oFormationCourante))
			$this->oFormationSrc = $this->oProjet->oFormationCourante;
		else if (count($this->aoFormationsSrc) > 0)
			$this->oFormationSrc = $this->aoFormationsSrc[0];
		
		if (!$this->oFormationSrc || !$this->oFormationSrc->oEnregBdd)
			$this->declarerErreur('erreurFormationSrc', TRUE,
                                  "Aucune formation n'est disponible comme source de la copie");
		
		// sélectionner la formation cible: choisie, courante dans Esprit, ou la 1ère de la liste
		if (!empty($this->iIdFormationDest))
			$this->oFormationDest = new CFormation($this->oProjet->oBdd, $this->iIdFormationDest);
		else if (!empty($this->oProjet->oFormationCourante))
			$this->oFormationDest = $this->oProjet->oFormationCourante;
		else if (count($this->aoFormationsDest) > 0)
			$this->oFormationDest = $this->aoFormationsDest[0];
		
		if (!$this->oFormationDest || !$this->oFormationDest->oEnregBdd)
			$this->declarerErreur('erreurFormationDest', TRUE,
                                  "Aucune formation n'est disponible comme cible de la copie");
		
		//// permission formations src/dest ???

		// si Copier déclenché mais aucune branche source sélectionnée
		if ($this->sAction == 'copier' && !count($this->aBranchesSrcSel))
			$this->declarerErreurAction('erreurBranchesSrc', FALSE, 'Aucun élément source sélectionné pour la copie');
			
		// si Coller déclenché mais...
		if ($this->sAction == 'coller')
		{
			// ...aucun éléments de presse-papiers sélectionné comme source
			if (empty($this->elemPpSel))
			{
				$this->declarerErreurAction('erreurElemPpCopie', FALSE, 
				                            "Aucun élément source (presse-papiers) sélectionné pour coller");
			}
			// ...ou aucun emplacement de destination sélectionné
			else if (empty($this->brancheDestSel))
			{
				$this->declarerErreurAction('erreurBrancheDest', FALSE,
			    	                        "Aucun emplacement cible sélectionné pour coller");
			}
			// ...ou l'emplacement de destination n'est pas de même type ou de type parent
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
		
		// si supprimer élément de presse-papier mais aucun élément sélectionné
		if ($this->sAction == 'supprimerElemPp' && empty($this->elemPpSel))
			$this->declarerErreurAction('erreurElemPpSuppr', FALSE, 'Aucun élément sélectionné pour la suppression');
	}
	
	/**
	 * @see	AfficheurPage#gererActions()
	 */
	function gererActions()
	{
		switch($this->sAction)
		{
			// copier une branche dans le press-papiers (le niveau Formation est "zappé")
			case 'copier':
				foreach ($this->aBranchesSrcSel as $elem)
				{
					list($iTypeBranche,) = explode('_', $elem);
					if ($iTypeBranche > TYPE_FORMATION)
						$this->oPressePapiers->ajouterElement(new PressePapiersElement($elem, 'copier'));
				}
				break;
			
			// coller la branche à l'emplacement destination, soit en dernier élément d'un élément parent,
			// ou devant un élément de même type
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
	
	/**
	 * @see AfficheurPage#afficherParties()
	 */
	function afficherParties()
	{
		$this->tpl->remplacer('{ongletCourant}', $this->sOngletCourant);
		
		$this->afficherFormationsSrcEtDest();
		$this->afficherPressePapiers();
	}
	
	/**
	 * Affiche les données relatives aux panneaux Copier (formation source) et Coller (formation cible) 
	 */
	function afficherFormationsSrcEtDest()
	{
		// données pour la boucle qui va suivre (panneau Copier/Src, puis Coller/Dest)
		$aDonnees = array(array('Src', &$this->aoFormationsSrc, &$this->oFormationSrc),
		                  array('Dest', &$this->aoFormationsDest, &$this->oFormationDest)
		                 );
		
		foreach ($aDonnees as $donnees)
		{
			// affichage de la liste des formations (sources ou cibles)
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
			
			// affichage des branches/nivaux (=contenu) de la formation active
			$tplBranche = new TPL_Block_ListeComposite("form{$donnees[0]}_branche", $this->tpl);
			$tplBranche->beginLoop();
			
			$itrFormation = new IterateurRecursif(new IterateurElementFormation($donnees[2]),
			                                      ITR_REC_PARENT_AVANT);
			for ($i = 0; $itrFormation->estValide(); $i++)
			{
				if ($i > 0)
				{
					$branche = $itrFormation->courant();
					$iNiv = $itrFormation->retNiveau() + 1;
				}
				else
				{
					$branche = $donnees[2];
					$iNiv = 0;
				}
				
				$tplBranche->nextLoop(TRUE, $iNiv);

				$idCompose = $branche->retTypeNiveau().'_'.$branche->retId();
				
				$tplBranche->remplacer('{branche.numNiv}', $branche->retTypeNiveau());
				$tplBranche->remplacer('{branche.symbole}', $branche->retSymbole());
				$tplBranche->remplacer('{branche.id}', $donnees[0].'_'.$idCompose);
				$tplBranche->remplacer('{branche.val}', $idCompose);
				$tplBranche->remplacer('{branche.intitule}', $branche->retTexteIntitule(TRUE, TRUE));
				$tplBranche->remplacer('{branche.titre}', $branche->retNom());
				
				if ($donnees[0] == 'Src' && $iNiv == 0)
					$tplBranche->remplacer('<input ', '<input disabled="disabled" ');
				if ($donnees[0] == 'Dest' && $idCompose == $this->brancheDestSel)
					$tplBranche->remplacer('<input ', '<input checked="checked" ');
				
				if ($iNiv > 0)
					$itrFormation->suiv();
			}
			$tplBranche->afficher();
		}
	}
	
	/**
	 * Affiche le panneau Contenu du presse-papiers
	 */
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
			$tplPressePapiers->remplacer('{pp.numNiv}', $elem->retTypeNiveau());
			$tplPressePapiers->remplacer('{pp.symbole}', $elem->retSymbole());
			$tplPressePapiers->remplacer('{pp.id}', 'Pp_'.$idCompose);
			$tplPressePapiers->remplacer('{pp.val}', $idCompose);
			$tplPressePapiers->remplacer('{pp.intitule}', $elem->retTexteIntitule(TRUE, TRUE));
			$tplPressePapiers->remplacer('{pp.titre}', $elem->retNom());
			
			if ($idCompose == $this->elemPpSel)
				$tplPressePapiers->remplacer('<input ', '<input checked="checked"');
		}
		$tplPressePapiers->afficher();
	}
}

// il faut instancier la classe et demander l'affichage
$page = new CopierCollerFormation();
$page->demarrer();

//// - vérif que la formation cible est modifiable par l'utilisateur (quelle méthode? dans quelle classe? il y a 
////   verifModifierFormation() dans CProjet, mais c'est pour la formation courante, pas n'importe laquelle)
//// ??? faire disparaître les boutons Choisir si JS activé (submit auto)
?>