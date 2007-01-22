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
$oTpl = new Template("formulaire_liste.tpl");
$oBlockIntro = new TPL_Block("BLOCK_INTRO",$oTpl);
$oBlockFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);

if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES')) // Verification de la permission d'utiliser le concepteur de formulaire
{
	// Récupération des variables
	if (isset($_GET['idformulaire']))
	{
		$v_iIdObjForm = $_GET['idobj'];
		$v_iIdFormulaire = $_GET['idformulaire'];
		if(isset($_GET['action']))
			$v_sAction = $_GET['action'];
		else
			$v_sAction = "";
		if(isset($_GET["iPasRechFrModif"]) && $_GET["iPasRechFrModif"]==1)
			$iPasRechFrModif = 1;
		else
			$iPasRechFrModif = 0;
	}
	else
	{
		$v_iIdFormulaire = 0;
		$v_iIdObjForm = 0;
		$v_sAction = "";
	}
	if(isset($_GET['bMesForms']))
		$bMesForms = $_GET['bMesForms'];
	else
		$bMesForms = 0;
	if ($v_iIdFormulaire > 0)
	{
		$oBlockIntro->effacer();
		$oBlockFormulaire->afficher();
		if($v_sAction == "copier")
		{
			if ($v_iIdObjForm > 0)
			{
				$v_iIdObjForm = CopierUnObjetFormulaire($oProjet->oBdd, $v_iIdObjForm, $v_iIdFormulaire, "max");
			}
		}
		else
		{
			if($v_sAction == "supprimer")
			{
				$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
				switch($oObjetFormulaire->retIdTypeObj())
				{
					case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd,$v_iIdObjForm);
							$oQTexteLong->effacer();
							break;
					
					case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$v_iIdObjForm);
							$oQTexteCourt->effacer();
							break;
					
					case 3:	$oQNombre = new CQNombre($oProjet->oBdd,$v_iIdObjForm);
							$oQNombre->effacer();
							break;
					
					case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$v_iIdObjForm);
							$oQListeDeroul->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepPoidsObj($v_iIdObjForm);
							break;
					
					case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$v_iIdObjForm);
							$oQRadio->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepPoidsObj($v_iIdObjForm);						 
							break;
					
					case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$v_iIdObjForm);
							$oQCocher->effacer();
							$oReponse = new CReponse($oProjet->oBdd);
							$oReponse->effacerRepPoidsObj($v_iIdObjForm);
							break;
					
					case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$v_iIdObjForm);
							$oMPTexte->effacer();
							break;
					
					case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$v_iIdObjForm);
							$oMPSeparateur->effacer();
							break;			   
				}
				$oObjetFormulaire->effacer();
				$v_iIdObjForm = 0;
			}
		}
		// Lecture de la table formulaire pour y récupérer les données de mise en page
		$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
		$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
		$iInterElem = $oFormulaire->retInterElem();
		$iInterEnonRep = $oFormulaire->retInterEnonRep();
		$iIdPersForm = $oFormulaire->retIdPers();
		if ($oFormulaire->retEncadrer() == 1)				//Vérifie s'il faut encadrer le titre ou non et compose le code html
			$sEncadrer = "style=\"border:1px solid black;\"";
		else
			$sEncadrer = "";
		$iLargeur = $oFormulaire->retLargeur();
		if ($oFormulaire->retTypeLarg() == "P") //Pourcentage ou pixel
			$sLargeur = $iLargeur."%";
		else
			$sLargeur = $iLargeur."px";
		$oTpl->remplacer("{sLargeur}",$sLargeur);
		$oTpl->remplacer("{iInterEnonRep}",$iInterEnonRep);
		$oTpl->remplacer("{iInterElem}",$iInterElem);
		
		//On vérifie si la personne peut voir OU modifier le formulaire ! les modifs se font a 2 endroits
		$iIdPers = $oProjet->oUtilisateur->retId();
		// si $v_iIdObjForm = 0 cela veut dire que l'on vient de selectionner le formulaire via le menu et alors :
		// on charge la modif du titre formulaire dans la frame du dessous[modif] uniquement si on est le propriétaire du formulaire où
		//si l'on est administrateur
		if ($_GET["verifUtilisation"] == 1)
		{
			$iNbUtilisations = $oFormulaire->retNbUtilisationsDsSessions();
			$iNbRemplis = $oFormulaire->retNbRemplisDsSessions();
			$sJsVerifUtilisation = " alerteFormulaireUtilise({$iNbUtilisations},{$iNbRemplis});";
		}
		if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) or ($iIdPersForm == $iIdPers) )
		{
			if($v_sAction == "copier")
				$oTpl->remplacer("{onload}","onload =\"allerAPos($v_iIdObjForm); selectionobj($v_iIdFormulaire,$v_iIdObjForm,$bMesForms);{$sJsVerifUtilisation}\"");
			else
				if($v_sAction == "supprimer")
					$oTpl->remplacer("{onload}","onload =\"allerAPos(); selectionobj($v_iIdFormulaire,$v_iIdObjForm,$bMesForms);{$sJsVerifUtilisation}\"");
				else
					$oTpl->remplacer("{onload}","onload =\"allerAPos();{$sJsVerifUtilisation}\"");
		}
		else
		{
			$oTpl->remplacer("{onload}","");
		}
		$aoObjetFormulaire = $oFormulaire->retListeObjetFormulaire();
		
		if ($v_iIdObjForm == 0) {$sCocher = "checked=\"checked\"";} else {$sCocher = "";}	// utile si on arrive sur la liste après suppression d'un objet par exemple
																							// cela permet de cocher le bouton radio devant le titre sans intervention de l'utilisateur
		//Si on clique sur le titre on envoie à la page 'formulaire_modif.php' via javascript 
		//idobj=0 et le numéro de formulaire 
		if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) or ($iIdPersForm == $iIdPers) )
			$sSelectModifTitre = "<input type=\"radio\" name=\"objet\" value=\"TitreFormulaire\" onclick =\"selectionobj($v_iIdFormulaire,0,$bMesForms)\" $sCocher />\n";
		else
			$sSelectModifTitre = "";
		$oTpl->remplacer("{sSelectModifTitre}",$sSelectModifTitre);
		$oTpl->remplacer("{sEncadrer}",$sEncadrer);
		$oTpl->remplacer("{sTitre}",$sTitre);
		
		$sHtmlListeObjForm = "";
		foreach($aoObjetFormulaire as $oObjetFormulaire)
		{
			$iIdObjActuel = $oObjetFormulaire->retId();
			$iOrdreObjForm = $oObjetFormulaire->retOrdreObjForm();
			
			$sHtmlListeObjForm .= "\n<a name=\"$iIdObjActuel\"></a>\n";
			
			if ($iIdObjActuel == $v_iIdObjForm)
				$sCocher = "checked=\"checked\"";
			else
				$sCocher = "";
			
			if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) || ($iIdPersForm == $iIdPers) )
				$sSelectModif = "<input type=\"radio\" name=\"objet\" value=\"$iIdObjActuel\" onclick =\"selectionobj($v_iIdFormulaire,$iIdObjActuel,$bMesForms)\" $sCocher /><b>$iOrdreObjForm</b>";
			else 
				$sSelectModif = "";
			
			switch($oObjetFormulaire->retIdTypeObj())
			{
				case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQTexteLong->cHtmlQTexteLong()."\n";
						break;
					
				case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQTexteCourt->cHtmlQTexteCourt()."\n";					
						break;
					
				case 3:	$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQNombre->cHtmlQNombre()."\n";					
						break;
					
				case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQListeDeroul->cHtmlQListeDeroul()."\n";
						break;
					
				case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQRadio->cHtmlQRadio()."\n";
						break;
					
				case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oQCocher->cHtmlQCocher()."\n";
						break;
					
				case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oMPTexte->cHtmlMPTexte()."\n";
						break;
					
				case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= $sSelectModif.$oMPSeparateur->cHtmlMPSeparateur()."\n";
						break;
			}
			$sHtmlListeObjForm .= "<div class=\"InterObj\"></div>\n";
		}
		$oTpl->remplacer("{ListeObjetFormul}",$sHtmlListeObjForm);
	}
	else
	{
		$oBlockFormulaire->effacer();
		$oBlockIntro->afficher();
		$oTpl->remplacer("{sLargeur}","0");
		$oTpl->remplacer("{iInterEnonRep}","0");
		$oTpl->remplacer("{iInterElem}","0");
		$oTpl->remplacer("{onload}","");
	}
	$oTpl->afficher();
}
?>
