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
 * @file	NavigateurFichiers.php
 */

require_once dirname(__FILE__).'/../globals.inc.php';
require_once dir_include('template.inc.php', TRUE);
require_once dir_lib('zip.class.php', TRUE);
require_once dir_lib('std/AfficheurPage.php', TRUE);
require_once dir_lib('std/FichierInfo.php', TRUE);
require_once dir_lib('std/IterateurDossier.php', TRUE);
require_once dir_lib('std/PressePapiers.php', TRUE);

/**
 * Contrôleur pour l'outil "navigateur de fichiers"
 */
class NavigateurFichiers extends AfficheurPage
{
	var $oDossierRacine;			///< Objet FichierInfo représentant le dossier racine (panneau Arborescence)
	var $oDossierCourant;			///< Objet FichierInfo représentant le dossier actif (panneau Contenu)
	var $aFichiersSel;				///< Tableau des fichiers sélectionnés (formulaire)
	var $oPressePapiers;			///< Objet représentant le presse-papiers
	var $sDossierACreer;			///< Lors de l'action Créer un dossier, objet représentant ce dossier
	var $oFichierARenommer;			///< Lors de l'action Renommer, objet représentant le fichier/dossier à renommer
	var $sFichierRenomme;			///< Nouveau nom pour le fichier/dossier à renommer
	var $oFichierATelecharger;		///< Lors de l'action télécharger, objet représentant le fichier à télécharger
	var $sFichierDeposeNom;			///< Lors de l'action Déposer un fichier, nom de ce dernier
	var $bDezipperFichierDepose;	///< Lors de l'action Déposer un fichier, indique si on doit dézipper s'il s'agit d'un fichier zippé
	var $sFiltreFichiers;			///< Regex représentant les noms de fichiers/dossiers sur lesquels les actions sont interdites (non défini ici, destiné aux classes filles)
	
	/**
	 * @see AfficheurPage#recupererDonnees()
	 */
	function recupererDonnees()
	{
		$sActionsReconnues = array('copier', 'couper', 'coller', 'supprimer', 'creerDossier', 'viderPressePapiers', 
		                           'renommer', 'annuler', 'telecharger', 'deposer');
		$this->sAction = array_shift(array_intersect($sActionsReconnues, array_keys($this->aDonneesForm)));

		$this->oDossierRacine = new FichierInfo(!empty($this->aDonneesUrl['r']) ? $this->aDonneesUrl['r'] : '');
		$this->oDossierCourant = new FichierInfo($this->oDossierRacine->formerChemin($this->aDonneesUrl['d']));
		$this->aFichiersSel = !empty($this->aDonneesForm['fichiers']) ? $this->aDonneesForm['fichiers'] : array();
	
		$this->oPressePapiers =& $this->aDonneesPersist['pressePapiers'];
		if (!isset($this->oPressePapiers))
			$this->oPressePapiers = new PressePapiers();
		
		$this->sDossierACreer = $this->aDonneesForm['nomDossierACreer'];
		
		$sFichierARenommer = is_array($this->aDonneesForm['renommer']) ?
		                     array_shift(array_keys($this->aDonneesForm['renommer'])): '';
		$this->oFichierARenommer = new FichierInfo($this->oDossierCourant->formerChemin($sFichierARenommer));
		$this->sFichierRenomme = $this->aDonneesForm['fichierRenomme'];
		
		$sFichierATelecharger = is_array($this->aDonneesForm['telecharger']) ? 
		                        array_shift(array_keys($this->aDonneesForm['telecharger'])) : '' ;
		$this->oFichierATelecharger = new FichierInfo($this->oDossierCourant->formerChemin($sFichierATelecharger));
		
		$this->bDezipperFichierDepose = ($this->aDonneesForm['dezipperFichierDepose'] == '1');
	}
	
	/**
	 * @see AfficheurPage#validerDonnees()
	 */
	function validerDonnees()
	{
		// le dossier racine doit être un dossier, et être lisible
		if (!$this->oDossierRacine->estDossier() || !$this->oDossierRacine->estLisible())
			$this->declarerErreur('erreurDossierRacine', TRUE);
		// le dossier courant doit être un dossier, lisible, ET identique ou descendant du dossier racine
		if (!$this->oDossierCourant->estDossier() || !$this->oDossierCourant->estLisible()
		    || !$this->oDossierCourant->estDescendantDe($this->oDossierRacine->retChemin()))
			$this->oDossierCourant->defChemin($this->oDossierRacine->retChemin());
		
		// des fichiers/dossiers doivent être choisis pour copier/coller/supprimer, ET ne pas faire partie de la liste des éléments "interdits"
		if (in_array($this->sAction, array('copier', 'couper', 'supprimer')))
		{
			if (!count($this->aFichiersSel))
			{
				$this->declarerErreurAction('erreurFichiersSel');
			}
			else if (!empty($this->sFiltreFichiers)
			         && count(($aFichiersAEnlever = preg_grep($this->sFiltreFichiers, $this->aFichiersSel))) > 0)
			{
				$this->aFichiersSel = array_diff($this->aFichiersSel, $aFichiersAEnlever);
				$this->declarerErreur('erreurFichiersProteges');
			}
		}
		
		// pour Coller, le presse-papiers doit contenir qq chose
		if ($this->sAction == 'coller' && $this->oPressePapiers->estVide())
			$this->declarerErreurAction('erreurPressePapiersVide');
		
		// le nom du dossier à créer doit être non vide, et ne pas faire partie de la liste de éléments "interdits"
		if ($this->sAction == 'creerDossier')
		{
			if (empty($this->sDossierACreer))
				$this->declarerErreurAction('erreurDossierACreerVide');
			else if (!empty($this->sFiltreFichiers) && preg_match($this->sFiltreFichiers, $this->sDossierACreer) > 0)
				$this->declarerErreurAction('erreurFichiersProteges');
			else
				// ne garder que le nom => pas de possibilité de retaper des /.../...
				$this->sDossierACreer = basename($this->sDossierACreer);
		}
		
		// le fichier/dossier à renommer doit être accessible en écriture, être descendant du dossier actif, et ne pas faire partie des éléments "interdits" 
		if ($this->sAction == 'renommer')
		{
			if (!$this->oFichierARenommer->estModifiable()
			    || !$this->oFichierARenommer->estDescendantDe($this->oDossierCourant->retChemin()))
				$this->declarerErreurAction('erreurFichierARenommer');
			else if (!empty($this->sFiltreFichiers)
			         && (preg_match($this->sFiltreFichiers, $this->oFichierARenommer->retChemin()) > 0
			             || preg_match($this->sFiltreFichiers, $this->sFichierRenomme) > 0))
				$this->declarerErreurAction('erreurFichiersProteges');
			 
			if (!empty($this->sFichierRenomme))
				$this->sFichierRenomme = basename($this->sFichierRenomme);
		}
		
		// le fichier à télécharger doit être un fichier, lisible, être descendant du dossier racine, et ne pas être un fichier PHP
		if ($this->sAction == 'telecharger')
		{
			if (!$this->oFichierATelecharger->estFichier() || !$this->oFichierATelecharger->estLisible()
			    || !$this->oFichierATelecharger->estDescendantDe($this->oDossierRacine->retChemin()))
				$this->declarerErreurAction('erreurTelechargement');
			else
			{
				$sRegExpExts = '%^php[0-9]*$%i';							
				if (preg_match($sRegExpExts, $this->oFichierATelecharger->retExtension()) > 0)
					$this->declarerErreurAction('erreurTelechargementInterdit');
			}
		}
		
		// le fichier déposé est-il valide ?
		if ($this->sAction == 'deposer')
		{
			if (!empty($_FILES['fichierDepose']['size']))
			{
				$this->sFichierDeposeCheminTemp = $_FILES['fichierDepose']['tmp_name'];
				$this->sFichierDeposeNom = basename($_FILES['fichierDepose']['name']);
			}
			else
			{
				$this->declarerErreurAction('erreurDeposer');
			}
		}
	}
	
	/**
	 * @see AfficheurPage#gererActions()
	 */
	function gererActions()
	{
		switch ($this->sAction)
		{
			case 'copier':
			case 'couper':
				$this->oPressePapiers->vider();
				foreach ($this->aFichiersSel as $fichier)
					$this->oPressePapiers->ajouterElement(new PressePapiersElement($fichier, $this->sAction));
				break;
				
			case 'supprimer':
				foreach($this->aFichiersSel as $sFichier)
				{
					$fichier = new FichierInfo($sFichier);
					$fichier->supprimer(TRUE); // suppression récursive
				}
				break;
				
			case 'coller':
				for ($itr = $this->oPressePapiers->retIterateur(); $itr->estValide(); $itr->suiv())
				{
					$elem = $itr->courant();
					$fichier = new FichierInfo($elem->retSujet());
					$action = $elem->retAction();
					
					// si les éléments collés n'existent plus ou si l'opération s'est bien déroulée, ils sont enlevés du presse-papiers
					if ($action == 'copier')
					{
						if (!$fichier->existe()
						    || $fichier->copier($this->oDossierCourant->retChemin(), TRUE, TRUE, TRUE))
							$this->oPressePapiers->enleverElement($elem, TRUE);
					}
					else if ($action == 'couper')
					{
						if (!$fichier->existe() || $fichier->deplacer($this->oDossierCourant->retChemin()))
							$this->oPressePapiers->enleverElement($elem, TRUE);
					}
					
					$this->oPressePapiers->enleverElementsDiffere();
				}
				break;
				
			case 'creerDossier':
				$this->oDossierCourant->creerDossier($this->sDossierACreer);
				break;
				
			case 'renommer':
				// l'action renommer a deux étapes possibles:
				//   - l'une sans la variable de formulaire avec le nouveau nom du fichier/dossier
				//     => à cette étape on ne fait rien d'autre qu'afficher un formulaire HTML spécial pour le renommage 
				//   - l'autre avec en plus le nouveau nom du fichier/dossier, qui provient du formulaire de la 1ère 
				//     => on renomme effectivement le fichier, et on remet l'action à "rien" pour que l'affichage soit 
				//        celui du navigateur de fichiers classique
				if (!empty($this->sFichierRenomme))
				{
					$oFichierRenomme = new FichierInfo($this->oDossierCourant->formerChemin($this->sFichierRenomme));
					if ($oFichierRenomme->existe())
						$this->declarerErreur('erreurFichierRenomme');
					else
						$this->oFichierARenommer->renommer($this->sFichierRenomme);
					
					$this->sAction = '';
				}
				break;
			
			case 'telecharger':
				header('Pragma: public');
				header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');
				header('Content-Length: '.$this->oFichierATelecharger->retTaille());
				header('Content-Tranfer-Encoding: none');
				header('Content-Type: application/octetstream; name="'.$this->oFichierATelecharger->retNom().'"');
				header('Content-Disposition: attachment; filename="'.$this->oFichierATelecharger->retNom().'"');
				header('Expires: 0');
				readfile($this->oFichierATelecharger->retChemin());
				exit();
				break;
				
			case 'deposer':
				$oFichierDepose = new FichierInfo($this->oDossierCourant->formerChemin($this->sFichierDeposeNom));
				if (!@move_uploaded_file($this->sFichierDeposeCheminTemp, $oFichierDepose->retChemin()))
				{
					$this->declarerErreur('erreurDeposer');
				}
				else
				{
					if (strtolower($oFichierDepose->retExtension()) == 'zip' && $this->bDezipperFichierDepose)
					{
						// dézipper avec écrasement de l'existant
						$zip = new CZip($oFichierDepose->retChemin());
						if ($zip->desarchiver($this->oDossierCourant->retChemin(), TRUE) <= 0)
							$this->declarerErreur('erreurDezip');

						$oFichierDepose->supprimer();
					}
				}
				break;
			
			case 'viderPressePapiers':
				$this->oPressePapiers->vider();
				break;
		}
	}
	
	/**
	 * @see AfficheurPage#afficherParties()
	 */
	function afficherParties()
	{
		$this->tpl->remplacer('{g:page}', $_SERVER['PHP_SELF']);
		$this->tpl->remplacer('{g:racine}', urlencode($this->oDossierRacine->retChemin()));
		$this->tpl->remplacer('{g:dossierCourant}', urlencode($this->oDossierCourant->retNom()));
		
		$this->afficherArborescence();
		$this->afficherContenu();
		$this->afficherPressePapiers();
	}
	
	/**
	 * Affiche le panneau contenant l'arborescence des dossiers à partir du 
	 * dossier racine
	 */
	function afficherArborescence()
	{
		$tplListeDossiers = new TPL_Block_ListeComposite('liste_dossiers', $this->tpl);
		$tplListeDossiers->beginLoop();
		
		$itrArborescence = new IterateurRecursif(new IterateurDossier($this->oDossierRacine->retChemin(), '*',
		                                                              TRUE, TRUE), 
		                                         ITR_REC_PARENT_AVANT);
		for ($bRacine = TRUE; $itrArborescence->estValide(); )
		{
			if (!$bRacine)
			{
				$dossier = $itrArborescence->courant();
				// niveau + 1 car on a déjà affiché la racine en 0
				$iNiv = $itrArborescence->retNiveau() + 1;
			}
			else
			{
				$dossier = $this->oDossierRacine;
				$iNiv = 0;
				$bRacine = FALSE;
			}
		
			$tplListeDossiers->nextLoop(TRUE, $iNiv);
			$tplListeDossiers->remplacer('{dossier.cheminComplet}', $dossier->retChemin());
			$tplListeDossiers->remplacer('{dossier.nom}', $dossier->retNom());
			$tplListeDossiers->remplacer('{dossier.url}', 
			                             urlencode($dossier->reduireChemin($this->oDossierRacine->retChemin())));
			if ($dossier->retChemin() == $this->oDossierCourant->retChemin())
				$tplListeDossiers->remplacer('class="dossier"', 'class="dossier actif" ');
				
			if ($iNiv > 0)
				$itrArborescence->suiv();
		}
		$tplListeDossiers->afficher();
	}
	
	/**
	 * Affiche le panneau du contenu du dossier actif
	 */
	function afficherContenu()
	{
		$itrContenu = new IterateurDossier($this->oDossierCourant->retChemin(), '*', TRUE);
		$tplListeContenu = new TPL_Block('liste_contenu', $this->tpl);
		$tplListeContenu->beginLoop();
		for (; $itrContenu->estValide(); $itrContenu->suiv())
		{
			$fichier = $itrContenu->courant();
			$fichierId = md5($fichier->retChemin());
			$bEstDossier = $fichier->estDossier();
			
			$tplListeContenu->nextLoop();
			// si on n'est pas déjà en action de renommage, on affiche à côté de chaque fichier/dossier les boutons Renommer et Télécharger (seulement fichiers)
			if ($this->sAction != 'renommer' || $fichier->retNom() != $this->oFichierARenommer->retNom())
			{
				$tplListeContenu->desactiverBloc('lc_ren');
				$tplListeContenu->activerBloc('lc_normal');
				$tplListeContenu->activerBloc('lc_btn_ren', $this->sAction != 'renommer');
				$tplListeContenu->activerBloc('lc_btn_tel', !$bEstDossier);
				$tplListeContenu->remplacer('{fichier.id}', 'dest_id_'.$fichierId);
			}
			// si on renomme un dossier/fichier, seuls des boutons Ok/Annuler sont affichés à côté
			else
			{
				$tplListeContenu->desactiverBloc('lc_normal');
				$tplListeContenu->activerBloc('lc_ren');
			}
			
			$tplListeContenu->remplacer('{fichier.cheminComplet}', $fichier->retChemin());
			$tplListeContenu->remplacer('{fichier.nom}', $fichier->retNom());
			if ($bEstDossier)
				$tplListeContenu->remplacer('class="fichier"', 'class="dossier"');
		}
		$tplListeContenu->afficher();
	}
	
	/**
	 * Affiche le panneau du contenu du presse-papiers
	 */
	function afficherPressePapiers()
	{
		$itrPressePapiers = $this->oPressePapiers->retIterateur();
		$tplPressePapiers = new TPL_Block('pp_element', $this->tpl);
		$tplPressePapiers->beginLoop();
		for (; $itrPressePapiers->estValide(); $itrPressePapiers->suiv())
		{
			$elem = $itrPressePapiers->courant();
			$fichier = new FichierInfo($elem->retSujet());
			$tplPressePapiers->nextLoop();
			$tplPressePapiers->remplacer('{pp.fichier}', $fichier->reduireChemin($this->oDossierRacine->retChemin()));
			$tplPressePapiers->remplacer('{pp.action}', $elem->retAction());
		}
		$tplPressePapiers->afficher();
	}
}

//// bouton Ok défaut quand Renommer
//// JS: lien/bouton "Sélectionner tout"
//// afficher les erreurs différemment? (texte en PHP et juste le div vide en HTML. Effacement du div si pas d'erreurs?)
//// bread crumbs en haut?
//// transformer Contenu en TABLE/TRs, pour aligner les boutons, et ajouter la taille (et date?)
//// action choisir un fichier/dossier (plus: télécharger un .zip de la sélection)

//// filtrer AUSSI sur les fichiers/dossiers dans les .zip (et sur l'action coller?)
//// sur les fichiers cochés, appeler FichierInfo->estDescendantDe(DossierRacine)
//// création/renommage: essayer noms spéciaux
?>