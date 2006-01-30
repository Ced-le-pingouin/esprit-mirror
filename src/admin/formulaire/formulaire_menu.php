<?php
require_once("globals.inc.php");
$oProjet = new CProjet();

if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES') || $oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))
{
	if (isset($HTTP_GET_VARS['idformulaire']))
		$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
	else if (isset($HTTP_POST_VARS['idformulaire']))
		$v_iIdFomulaire = $HTTP_POST_VARS['idformulaire'];
	/*else
	{
		echo "Pas de formulaire a supprimer";	
		$v_iIdFormulaire = 0;
	}*/
	
	if (!empty($v_iIdFormulaire) && isset($HTTP_GET_VARS['idobj']))
		$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
	else
		$v_iIdObjForm = 0;
	
	$bMesForms = $HTTP_GET_VARS['cbMesForms'];
	
	if ($HTTP_GET_VARS['typeaction']=='supprimer')
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
	
	if ($HTTP_GET_VARS['typeaction']=='copier')
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
	$oFormulaireSel = new CFormulaire($oProjet->oBdd, $v_iIdFormulaire);
	if ($bMesForms)
		$aoFormulairesVisibles = $oFormulaireSel->retListeFormulairesVisibles($iIdPersCourant);
	else if (!$oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES'))  //Si administrateur -> on voit tout les formulaires
		$aoFormulairesVisibles = $oFormulaireSel->retListeFormulairesVisibles($iIdPersCourant, 'public');
	else //Si concepteur -> on voit tout ses formulaires + les formulaires publics
		$aoFormulairesVisibles = $oFormulaireSel->retListeFormulairesVisibles(NULL, NULL, NULL, TRUE);
	
	$oTpl = new Template("formulaire_menu.tpl");
	$oBlock = new TPL_Block("BLOCK_FORM",$oTpl);

	$oTpl->remplacer("{id_obj}", $v_iIdObjForm);
	$oTpl->remplacer("{cbMesFormsCoche}", ($bMesForms?" CHECKED":""));
	
	$iLargeurMax = 28;
	$sSymboleDejaUtilise = "(!)";
	if (count($aoFormulairesVisibles))
	{
		$oBlock->beginLoop();
		
		foreach ($aoFormulairesVisibles as $oFormulaireCourant)
		{
			$oBlock->nextLoop();
			
			$sNomFormulaire = enleverBaliseMeta($oFormulaireCourant->retNom());
			if ($oFormulaireCourant->retNbUtilisationsDsSessions() || $oFormulaireCourant->retNbRemplisDsSessions())
				$sNomFormulaire = $sSymboleDejaUtilise.$sNomFormulaire;
			
			$sNomFormulaireCourt = $sNomFormulaire;
			
			if (strlen($sNomFormulaireCourt) > $iLargeurMax)
				$sNomFormulaireCourt = sprintf("%.".($iLargeurMax - 3)."s...", $sNomFormulaireCourt);
			
			$oBlock->remplacer("{nom_formulaire}", $sCodeHtml . htmlentities($sNomFormulaireCourt));
			
			$oBlock->remplacer("{infobulle_formulaire}", htmlentities($sNomFormulaire));
			$oBlock->remplacer("{id_formulaire}",$oFormulaireCourant->retId());
			if ($oFormulaireCourant->retId() == $oFormulaireSel->retId())
				$oBlock->remplacer("{select_form}", "SELECTED");
			else
				$oBlock->remplacer("{select_form}", "");
			
			if ($iIdPersCourant == $oFormulaireCourant->retIdPers())
			{
				$oBlock->remplacer("{couleur}","style=\"color:green;\"");
			}
			else
				$oBlock->remplacer("{couleur}","");
		}
		
		$oBlock->afficher();
	}
	else
	{
		$oBlock->effacer();
	}
	
	$sTitreHaut = "Formulaires";
	$oBlocForm = new TPL_Block("BLOC_FORM_COURANT", $oTpl);
	if ($oFormulaireSel->retId())
	{
		$oBlocForm->remplacer("{nom_form_courant}", $oFormulaireSel->retNom());
		$oBlocForm->afficher();
		
		$oTpl->remplacer("{id_formulaire_sel}", $oFormulaireSel->retId());
		$sTitreHaut .= " (".$oFormulaireSel->retNom();
	}
	else
		$oBlocForm->effacer();
	
	$oBlocElem = new TPL_Block("BLOC_ELEM_COURANT", $oTpl);
	if ($oFormulaireSel->retId())
	{
		$oBlocElemLiens = new TPL_Block("BLOC_ELEM_COURANT_LIENS", $oBlocElem);
		if ($v_iIdObjForm)
		{
			$oObjFormSel = new CObjetFormulaire($oProjet->oBdd, $v_iIdObjForm);
			$oTypeObj = new CTypeObjetForm($oProjet->oBdd, $oObjFormSel->retIdType());
			
			$oBlocElem->remplacer("{nom_elem_courant}", "Elément ".$oObjFormSel->retOrdre()."<br>(".$oTypeObj->retDescCourte().")");
			$oBlocElemLiens->afficher();
			$sTitreHaut .= " &raquo; Elément ".$oObjFormSel->retOrdre().")";
		}
		else
		{
			$oBlocElem->remplacer("{nom_elem_courant}", "-");
			$oBlocElemLiens->effacer();
			$sTitreHaut .= " &raquo; Options du formulaire)";
		}
		
		$oBlocElem->afficher();
	}
	else
		$oBlocElem->effacer();
	
	$oTpl->remplacer("{titre_haut}", addslashes($sTitreHaut));
	$oTpl->afficher();
	
	$oProjet->terminer();  //Ferme la connection avec la base de données
}//Verification de la permission d'utiliser le concepteur de formulaire
?>
