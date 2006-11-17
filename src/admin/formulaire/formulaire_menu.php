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

if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES') || $oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))
{
	$oTpl = new Template("formulaire_menu.tpl");
	$iIdPersCourant = $oProjet->oUtilisateur->retId();
	$sMessageEtat = "";
	
	if (isset($_GET['idformulaire']))
		$v_iIdFormulaire = $_GET['idformulaire'];
	
	if ($_GET['typeaction'] == 'supprimer')
	{
		if ($v_iIdFormulaire != NULL)  // Verification effectué aussi en javascript
		{
			$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
			$iIdPersForm = $oFormulaire->retIdPers();
			
			//Vérification si la personne peut supprimer le formulaire; on ne peut supprimer que ces propres formulaires
			//sauf l'administrateur qui peut tout supprimer
			if ( ($iIdPersCourant == $iIdPersForm) or ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) )
			{
				//Effacement des poids des réponses du formulaire
				$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
				$v_sListeAxes = "0";
				$oReponse_Axe->VerifierValidite($v_iIdFormulaire,$v_sListeAxes);
							  
				//Effacement des objets du formulaire 1 par 1
				$hResult = $oProjet->oBdd->executerRequete("SELECT * FROM ObjetFormulaire WHERE IdForm = $v_iIdFormulaire");
				
				while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
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
								$oReponse = new CReponse($oProjet->oBdd);
								$oReponse->effacerRepObj($iIdObjActuel);
								break;
						
						case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
								$oQRadio->effacer();
								$oReponse = new CReponse($oProjet->oBdd);
								$oReponse->effacerRepObj($iIdObjActuel);						 
								break;
						
						case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
								$oQCocher->effacer();
								$oReponse = new CReponse($oProjet->oBdd);
								$oReponse->effacerRepObj($iIdObjActuel);
								break;
						
						case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
								$oMPTexte->effacer();
								break;
						
						case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
								$oMPSeparateur->effacer();
								break;
					} //Fin switch
					$oObjetFormulaire->effacer();
				} //Fin while
				
				//Effacement des liens avec les axes du formulaire
				$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
				$oFormulaire_Axe->effacerAxesForm($v_iIdFormulaire);
				
				//Effacement du formulaire
				//$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
				$oFormulaire->effacer();
				
				//$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">alert('L\'activité a été supprimée avec succès');</script>";
			}
			else //Cas ou l'on a pas le droit de supprimer un formulaire 
			{
				$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">alert('Vous ne pouvez pas supprimer l\'activité, veuillez contacter votre administrateur pour plus d\'informations');</script>";
			}
		}
	}
	
	if ($_GET['typeaction'] == 'copier')
	{
		if ($v_iIdFormulaire != NULL)  // Verification effectué aussi en javascript
		{
			$iNouvIdFormul = CopierUnFormulaire($oProjet->oBdd,$v_iIdFormulaire,$iIdPersCourant);
			if($iNouvIdFormul > 0)
			{
				$v_iIdFormulaire = $iNouvIdFormul;
				$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerliste(0,$v_iIdFormulaire);</script>";
			}
			else
				  $sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">alert('Erreur lors de la copie, contactez votre administrateur !');</script>";
		}
	}
	
	if ($_GET['typeaction'] == 'ajouter')
	{
		$oFormulaire = new CFormulaire($oProjet->oBdd);
		$v_iIdFormulaire = $oFormulaire->ajouter($iIdPers);
		$sMessageEtat = "<script language=\"javascript\" type=\"text/javascript\">rechargerliste(0,$v_iIdFormulaire);</script>";
	}
	
	//Affichage du menu//
	$oFormulaire = new CFormulaire($oProjet->oBdd);
	if ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))  //Si administrateur -> on voit tout les formulaires
		$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles(NULL, NULL, NULL, TRUE);
	else //Si concepteur -> on voit tout ses formulaires + les formulaires publics
		$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles($iIdPersCourant, 'public');
	
	$oBlock = new TPL_Block("BLOCK_FORM",$oTpl);
	
	$iLargeurMax = 28;
	$sSymboleDejaUtilise = "(!)";
	if (count($aoFormulairesVisibles))
	{
		$oBlock->beginLoop();
		
		foreach ($aoFormulairesVisibles as $oFormulaireCourant)
		{
			$oBlock->nextLoop();
			
			$sNomFormulaire = enleverBaliseMeta($oFormulaireCourant->retTitre());
			if ($oFormulaireCourant->retNbUtilisationsDsSessions() || $oFormulaireCourant->retNbRemplisDsSessions())
				$sNomFormulaire = $sSymboleDejaUtilise.$sNomFormulaire;
			
			$sNomFormulaireCourt = $sNomFormulaire;
			if (function_exists('mb_strlen'))
			{
					if (mb_strlen($sNomFormulaireCourt,"UTF-8") > $iLargeurMax)
						$sNomFormulaireCourt = mb_substr($sNomFormulaireCourt,0,$iLargeurMax-3,"UTF-8")."...";
			}
			else // le résultat sera sûrement une chaîne corrompue et invalide, avec une représentation totalement incompréhensible
			{
					if (strlen($sNomFormulaireCourt) > $iLargeurMax)
						$sNomFormulaireCourt = sprintf("%.".($iLargeurMax - 3)."s...", $sNomFormulaireCourt);
			}
			
			$oBlock->remplacer("{nom_formulaire}", emb_htmlentities($sNomFormulaireCourt));
			$oBlock->remplacer("{infobulle_formulaire}", emb_htmlentities($sNomFormulaire));
			$oBlock->remplacer("{id_formulaire}",$oFormulaireCourant->retId());
			
			if ($iIdPersCourant == $oFormulaireCourant->retIdPers())
				$oBlock->remplacer("{couleur}","style=\"color:green;\"");
			else
				$oBlock->remplacer("{couleur}","");
			if($oFormulaireCourant->retId() == $v_iIdFormulaire)
				$oBlock->remplacer("{selected}"," selected=\"selected\"");
			else
				$oBlock->remplacer("{selected}","");
		}
		$oBlock->afficher();
	}
	else
	{
		$oBlock->effacer();
	}
	$oTpl->remplacer("{Message_Etat}",$sMessageEtat);
	$oTpl->afficher();
	$oProjet->terminer();
}//Verification de la permission d'utiliser le concepteur de formulaire
?>
