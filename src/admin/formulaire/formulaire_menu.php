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
	if (isset($_GET['idformulaire']))
	{
		$v_iIdFormulaire = @$_GET['idformulaire'];
	}
	else if (isset($_POST['idformulaire']))
	{
		$v_iIdFomulaire = @$_POST['idformulaire'];
	}
	/*else
	{
		echo "Pas de formulaire a supprimer";	
		$v_iIdFormulaire = 0;
	}*/
	
	if (@$_GET['typeaction']=='supprimer')
	{
		if ($v_iIdFormulaire == Null)  //Si on n'a pas sélectionné de formulaire dans la liste
		{
			  echo"<SCRIPT language=\"JavaScript\">";
			  echo "alert('Veuillez sélectionner un formulaire dans la liste');";
			  echo "</SCRIPT>";
		}
		else
		{
			$iIdPersCourant = $oProjet->oUtilisateur->retId();
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
					//echo "<br>iIdObjActuel : ".$iIdObjActuel;
					
					switch($oObjetFormulaire->retIdTypeObj())
					{
						case 1:
							//echo "Objet de type 1<br>";
							$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
							$oQTexteLong->effacer();
							break;
							 
						case 2:
							//echo "Objet de type 2<br>";
							$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
							$oQTexteCourt->effacer();
							break;
							 
						case 3:
							//echo "Objet de type 3<br>";
							$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
							$oQNombre->effacer();
							break;
							 
						case 4:
							//echo "Objet de type 4<br>";
							$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
							$oQListeDeroul->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepObj($iIdObjActuel);
							break;
							 
						case 5:
							//echo "Objet de type 5<br>";
							$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
							$oQRadio->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepObj($iIdObjActuel);						 
							break;
							
						case 6:
							//echo "Objet de type 6<br>";
							$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
							$oQCocher->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepObj($iIdObjActuel);
							break;
							
						case 7:
							//echo "Objet de type 7<br>";
							$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
							$oMPTexte->effacer();
							break;
							
						case 8:
							//echo "Objet de type 8<br>";
							$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
							$oMPSeparateur->effacer();
							break;
							
						default:
							echo "Erreur: Id d'objet de formulaire incorrect<br>";
					} //Fin switch
					
					$oObjetFormulaire->effacer();
				} //Fin while
				
				//Effacement des liens avec les axes du formulaire
				$oFormulaire_Axe = new CFormulaire_Axe($oProjet->oBdd);
				$oFormulaire_Axe->effacerAxesForm($v_iIdFormulaire);
				
				//Effacement du formulaire
				//$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
				$oFormulaire->effacer();
				
				echo"<SCRIPT language=\"JavaScript\">";
				echo "alert('Le formulaire a été supprimé avec succès');";
				echo "</SCRIPT>";
			}
			else //Cas ou l'on a pas le droit de supprimer un formulaire 
			{
				echo"<SCRIPT language=\"JavaScript\">";
				echo "alert('Vous ne pouvez pas supprimer le formulaire, veuillez contacter votre administrateur pour plus d\'informations.');";
				echo "</SCRIPT>";
			}
		}
	}
	
	if (@$_GET['typeaction']=='copier')
	{
		$iIdPersCourant = $oProjet->oUtilisateur->retId();
		
		if($v_iIdFormulaire == Null)
		{
			  echo"<SCRIPT language=\"JavaScript\">";
			  echo "alert('Veuillez sélectionner un formulaire dans la liste');";
			  echo "</SCRIPT>";
		}
		else
		{
			  if(CopierUnFormulaire($oProjet->oBdd,$v_iIdFormulaire,$iIdPersCourant))
			  {	
				  echo"<SCRIPT language=\"JavaScript\">";
				  echo "alert('La copie s\\'est correctement réalisée !');";
				  echo "</SCRIPT>";
			  }
			  else
			  {
				  echo"<SCRIPT language=\"JavaScript\">";
				  echo "alert('Erreur lors de la copie, contactez votre administrateur !');";
				  echo "</SCRIPT>";
			  }
		}
	}
	
	//Affichage du menu//
	$iIdPersCourant = $oProjet->oUtilisateur->retId();
	$oFormulaire = new CFormulaire($oProjet->oBdd);
	if ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))  //Si administrateur -> on voit tout les formulaires
		$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles(NULL, NULL, NULL, TRUE);
	else //Si concepteur -> on voit tout ses formulaires + les formulaires publics
		$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles($iIdPersCourant, 'public');
	
	$oTpl = new Template("formulaire_menu.tpl");
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
			
			$oBlock->remplacer("{nom_formulaire}", htmlentities($sNomFormulaireCourt,ENT_COMPAT,"UTF-8"));
			$oBlock->remplacer("{infobulle_formulaire}", htmlentities($sNomFormulaire,ENT_COMPAT,"UTF-8"));
			$oBlock->remplacer("{id_formulaire}",$oFormulaireCourant->retId());
			
			if ($iIdPersCourant == $oFormulaireCourant->retIdPers())
				$oBlock->remplacer("{couleur}","style=\"color:green;\"");
			else
				$oBlock->remplacer("{couleur}","");
		}
		$oBlock->afficher();
	}
	else
	{
		$oBlock->effacer();
	}
	$oTpl->afficher();
	$oProjet->terminer();
}//Verification de la permission d'utiliser le concepteur de formulaire
?>
