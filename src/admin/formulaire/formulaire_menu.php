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

require_once("globals.inc.php");
$oProjet = new CProjet();

if($oProjet->verifPermission('PERM_MOD_FORMULAIRES') || $oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))
{
	isset($_GET['idformulaire']) ? ($v_iIdFormulaire = $_GET['idformulaire']) : ($v_iIdFormulaire = 0);
	isset($_GET['idobj']) ? ($v_iIdObjForm = $_GET['idobj']) : ($v_iIdObjForm = 0);
	isset($_GET['bMesForms']) ? ($bMesForms = $_GET['bMesForms']) : ($bMesForms = 0);
	isset($_GET['typeaction']) ? ($sTypeAction = $_GET['typeaction']) : ($sTypeAction = NULL);
	$oTpl = new Template("formulaire_menu.tpl");
	$oBlocLienForm = new TPL_Block("BLOC_LIEN_FORM", $oTpl);
	$oBlocElem = new TPL_Block("BLOC_ELEM_COURANT", $oTpl);
	$oBlockSelFormul = new TPL_Block("BLOCK_SEL_FORM",$oTpl);
	$oTpl->remplacer("{bMesFormsCoche}",($bMesForms ? " checked=\"checked\"" : ""));
	$oTpl->remplacer("{idObjForm}",$v_iIdObjForm);
	$iIdPersCourant = $oProjet->oUtilisateur->retId();
	$sMessageEtat = ""; // variable qui peut contenir du javascript pour le rechargement de frame ou l'affichage de message d'erreur
	switch($sTypeAction) // gestion de l'ajout, de la copie et de la suppression d'un activité en ligne
	{
		case 'supprimer' :
			if($v_iIdFormulaire > 0)  // Vérification effectué aussi en javascript
			{
				$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
				$iIdPersForm = $oFormulaire->retIdPers();
				// Vérification si la personne peut supprimer l'activité: soit l'auteur ou soit l'administrateur peut supprimer
				if( ($iIdPersCourant == $iIdPersForm) or ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) )
				{
					// Effacement des poids des réponses du formulaire
					$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
					$v_sListeAxes = "0";
					$oReponse_Axe->VerifierValidite($v_iIdFormulaire,$v_sListeAxes);
					// Effacement des objets du formulaire 1 par 1
					$hResult = $oProjet->oBdd->executerRequete("SELECT * FROM ObjetFormulaire WHERE IdFormul = $v_iIdFormulaire");
					while( $oEnreg = $oProjet->oBdd->retEnregSuiv($hResult) )
					{
						$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd);
						$oObjetFormulaire->init($oEnreg);
						$iIdObjActuel = $oObjetFormulaire->retId();
						switch($oObjetFormulaire->retIdTypeObj())
						{
							case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
									$oQTexteLong->effacer();
									break;
							
							case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
									$oQTexteCourt->effacer();
									break;
							
							case 3:	$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
									$oQNombre->effacer();
									break;
							
							case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
									$oQListeDeroul->effacer();
									$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
									$oPropositionReponse->effacerRepObj($iIdObjActuel);
									break;
							
							case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
									$oQRadio->effacer();
									$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
									$oPropositionReponse->effacerRepObj($iIdObjActuel);						 
									break;
							
							case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
									$oQCocher->effacer();
									$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
									$oPropositionReponse->effacerRepObj($iIdObjActuel);
									break;
							
							case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
									$oMPTexte->effacer();
									break;
							
							case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
									$oMPSeparateur->effacer();
									break;
						}
						$oObjetFormulaire->effacer();
					}
					// Effacement des liens avec les axes du formulaire
					$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
					$oFormulaire_Axe->effacerAxesForm($v_iIdFormulaire);
					// Effacement du formulaire
					$oFormulaire->effacer();
					$v_iIdFormulaire = 0;
					$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerDroite(0,0,$bMesForms);</script>";
				}
				else // Cas ou l'on a pas le droit de supprimer un formulaire 
				{
					$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">alert('Vous ne pouvez pas supprimer l\'activité, veuillez contacter votre administrateur pour plus d\'informations');</script>";
				}
			}
			break;
		
		case 'copier' :
			if($v_iIdFormulaire > 0)  // Vérification effectué aussi en javascript
			{
				$iNouvIdFormul = CopierUnFormulaire($oProjet->oBdd,$v_iIdFormulaire,$iIdPersCourant);
				if($iNouvIdFormul > 0)
				{
					$v_iIdFormulaire = $iNouvIdFormul;
					$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerDroite($v_iIdFormulaire,0,$bMesForms);</script>";
				}
				else
					  $sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">alert('Erreur lors de la copie, contactez votre administrateur !');</script>";
			}
			break;
		
		case 'ajouter' :
			$oFormulaire = new CFormulaire($oProjet->oBdd);
			$v_iIdFormulaire = $oFormulaire->ajouter($iIdPersCourant);
			$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerDroite($v_iIdFormulaire,0,$bMesForms);</script>";
			break;
	}
	($v_iIdFormulaire > 0) ? ($oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire)) : ($oFormulaire = new CFormulaire($oProjet->oBdd));
	if($bMesForms)
		$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles($iIdPersCourant);
	else
		if(!$oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))  // Si administrateur -> on voit tout les formulaires
			$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles($iIdPersCourant, 'public');
		else // Si concepteur -> on voit tout ses formulaires + les formulaires publics
			$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles(NULL, NULL, NULL, TRUE);
	$iLargeurMax = 28;
	$sSymboleDejaUtilise = "(!)";
	$bFormulaireVisiblePersCourante = false; // booleen qui dit si l'activité courante est affichée
	if(count($aoFormulairesVisibles))
	{
		$oBlockSelFormul->beginLoop();
		foreach($aoFormulairesVisibles as $oFormulaireTmp)
		{
			$oBlockSelFormul->nextLoop();
			$sNomFormulaire = enleverBaliseMeta($oFormulaireTmp->retTitre());
			if($oFormulaireTmp->retNbUtilisationsDsSessions() || $oFormulaireTmp->retNbRemplisDsSessions() )
				$sNomFormulaire = $sSymboleDejaUtilise.$sNomFormulaire;
			$sNomFormulaireCourt = $sNomFormulaire;
			if(mb_strlen($sNomFormulaireCourt,"UTF-8") > $iLargeurMax)
				$sNomFormulaireCourt = mb_substr($sNomFormulaireCourt,0,$iLargeurMax-3,"UTF-8")."...";
			$oBlockSelFormul->remplacer("{nom_formulaire}", htmlentities($sNomFormulaireCourt,ENT_COMPAT,"UTF-8"));
			$oBlockSelFormul->remplacer("{infobulle_formulaire}", htmlentities($sNomFormulaire,ENT_COMPAT,"UTF-8"));
			$oBlockSelFormul->remplacer("{id_formulaire}",$oFormulaireTmp->retId());
			if($iIdPersCourant == $oFormulaireTmp->retIdPers() ) // auteur de l'activité ?
				$oBlockSelFormul->remplacer("{couleur}","style=\"color:green;\"");
			else
				$oBlockSelFormul->remplacer("{couleur}","");
			if($oFormulaireTmp->retId() == $v_iIdFormulaire) // activité courante ?
			{
				$oBlockSelFormul->remplacer("{selected}"," selected=\"selected\"");
				$bFormulaireVisiblePersCourante = true;
			}
			else
			{
				$oBlockSelFormul->remplacer("{selected}","");
			}
		}
		$oBlockSelFormul->afficher();
	}
	else
	{
		$oBlockSelFormul->effacer();
	}
	// Affichage du menu
	if($v_iIdFormulaire > 0 && $bFormulaireVisiblePersCourante)
	{
		$oBlocLienForm->afficher();
		$oBlocElem->afficher();
		$oTpl->remplacer("{id_formulaire_sel}",$v_iIdFormulaire);
		$oTpl->remplacer("{id_obj}",$v_iIdObjForm);
		$oTpl->remplacer("{bMesForms}",$bMesForms);
		$oBlocElemLiens = new TPL_Block("BLOC_ELEM_COURANT_LIENS", $oTpl);
		if($v_iIdObjForm > 0)
		{
			$oObjFormSel = new CObjetFormulaire($oProjet->oBdd, $v_iIdObjForm);
			if($oObjFormSel->retIdFormul() == $v_iIdFormulaire)
			{
				$oTpl->remplacer("{nom_elem_courant}", "Elément ".$oObjFormSel->retOrdre());
				$oBlocElemLiens->afficher();
			}
			else
			{
				$oTpl->remplacer("{nom_elem_courant}", "-");
				$oBlocElemLiens->effacer();
				$v_iIdObjForm = 0;
			}
		}
		else
		{
			$oTpl->remplacer("{nom_elem_courant}", "-");
			$oBlocElemLiens->effacer();
		}
	}
	else
	{
		$oBlocLienForm->effacer();
		$oBlocElem->effacer();
	}
	if($sTypeAction == 'selection')
	{
		if($bFormulaireVisiblePersCourante)
			$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerDroite($v_iIdFormulaire,$v_iIdObjForm,$bMesForms);</script>";
		else
			$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerDroite(0,0,$bMesForms);</script>";
	}
	$oTpl->remplacer("{Message_Etat}",$sMessageEtat);
	$oTpl->afficher();
}//Verification de la permission d'utiliser le concepteur de formulaire
$oProjet->terminer();
?>
