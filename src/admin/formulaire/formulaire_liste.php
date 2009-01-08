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
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$oPropositionReponse->effacerRepPoidsObj($v_iIdObjForm);
							break;
					
					case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$v_iIdObjForm);
							$oQRadio->effacer();
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$oPropositionReponse->effacerRepPoidsObj($v_iIdObjForm);						 
							break;
					
					case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$v_iIdObjForm);
							$oQCocher->effacer();
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$oPropositionReponse->effacerRepPoidsObj($v_iIdObjForm);
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
			$sEncadrer = "name=\"encadrer\"";
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
			$iOrdreObjForm = $oObjetFormulaire->retOrdreObjFormul();
			
			$sHtmlListeObjForm .= "\n<a name=\"$iIdObjActuel\"></a>\n";
			
			if ($iIdObjActuel == $v_iIdObjForm)
				$sCocher = "checked=\"checked\"";
			else
				$sCocher = "";
			
			if( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) || ($iIdPersForm == $iIdPers) )
				$sHtmlListeObjForm .= "&nbsp;<input type=\"radio\" name=\"objet\" value=\"$iIdObjActuel\" onclick =\"selectionobj($v_iIdFormulaire,$iIdObjActuel,$bMesForms)\" $sCocher /><b>$iOrdreObjForm</b>";
			
			switch($oObjetFormulaire->retIdTypeObj())
			{
				case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QTexteLong : $iIdObjActuel -->\n"
											."<div align=\"".$oQTexteLong->retAlignEnonQTL()."\">".convertBaliseMetaVersHtml($oQTexteLong->retEnonQTL())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQTexteLong->retAlignRepQTL()."\">\n"
											."<textarea name=\"$iIdObjActuel\" rows=\"".$oQTexteLong->retHauteurQTL()."\" cols=\"".$oQTexteLong->retLargeurQTL()."\">\n"
											."</textarea>\n"
											."</div><br />\n";
						break;
				
				case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QTexteCourt : $iIdObjActuel -->\n"
											."<div align=\"".$oQTexteCourt->retAlignEnonQTC()."\">".convertBaliseMetaVersHtml($oQTexteCourt->retEnonQTC())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQTexteCourt->retAlignRepQTC()."\">\n"
											.convertBaliseMetaVersHtml($oQTexteCourt->retTxtAvQTC())
											."<input type=\"text\" name=\"$iIdObjActuel\" size=\"".$oQTexteCourt->retLargeurQTC()."\" maxlength=\"".$oQTexteCourt->retMaxCarQTC()."\" />\n"
											.convertBaliseMetaVersHtml($oQTexteCourt->retTxtApQTC())
											."</div><br />\n";
						break;
				
				case 3:	$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QNombre : $iIdObjActuel -->\n"
											."<div align=\"".$oQNombre->retAlignEnonQN()."\">".convertBaliseMetaVersHtml($oQNombre->retEnonQN())."</div>"
											."<div class=\"InterER\" align=\"".$oQNombre->retAlignRepQN()."\">"
											.convertBaliseMetaVersHtml($oQNombre->retTxTAvQN())
											."<input type=\"text\" name=\"$iIdObjActuel\" size=\"10\" maxlength=\"10\""
											." id=\"id_".$oQNombre->retId()."_".$oQNombre->retNbMinQN()."_".$oQNombre->retNbMaxQN()."\" onchange=\"validerQNombre(this);\" />"
											.convertBaliseMetaVersHtml($oQNombre->retTxtApQN())
											."</div><br />\n";
						break;
				
				case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QListeDeroul : $iIdObjActuel -->\n"
											."<div align=\"".$oQListeDeroul->retAlignEnonQLD()."\">".convertBaliseMetaVersHtml($oQListeDeroul->retEnonQLD())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQListeDeroul->retAlignRepQLD()."\">\n"
											.convertBaliseMetaVersHtml($oQListeDeroul->retTxTAvQLD());
						$sHtmlListeObjForm .= "<select name=\"$iIdObjActuel\">\n";
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
									$sHtmlListeObjForm .= "<option value=\"".$oPropRep->retId()."\">".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."</option>\n";
						}
						$sHtmlListeObjForm .= "</select>\n"
											.convertBaliseMetaVersHtml($oQListeDeroul->retTxtApQLD())
											."</div>\n";
						break;
				
				case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QRadio : $iIdObjActuel -->\n"
											."<div align=\"".$oQRadio->retAlignEnonQR()."\">".convertBaliseMetaVersHtml($oQRadio->retEnonQR())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQRadio->retAlignRepQR()."\">\n"
											."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>\n"
											."<td valign=\"top\">".convertBaliseMetaVersHtml($oQRadio->retTxTAvQR())."</td>\n"
											."<td valign=\"top\">";
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if($oQRadio->retDispQR() == 'Ver')
							$sHtmlListeObjForm .= "<table cellspacing=\"0\" cellpadding=\"0\">";
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
							{
								if($oQRadio->retDispQR() == 'Ver')
									$sHtmlListeObjForm .= "<tr><td><input type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" "
											."value=\"".$oPropRep->retId()."\" /></td><td>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."</td></tr>\n";
								else
									$sHtmlListeObjForm .= "<input type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" value=\"".$oPropRep->retId()."\" />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."\n";
							}
						}
						if($oQRadio->retDispQR() == 'Ver')
							$sHtmlListeObjForm .= "</table>";
						$sHtmlListeObjForm .= "</td>\n"
											."<td valign=\"top\">".convertBaliseMetaVersHtml($oQRadio->retTxtApQR())."</td>\n"
											."</tr></table>\n"
											."</div>\n";
						break;
				
				case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "\n<!--QCocher : $iIdObjActuel -->\n"
											."<div align=\"".$oQCocher->retAlignEnonQC()."\">".convertBaliseMetaVersHtml($oQCocher->retEnonQC())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQCocher->retAlignEnonQC()."\">\n"
											."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>\n"
											."<td valign=\"top\">".$oQCocher->retTxTAvQC()."</td>\n"
											."<td valign=\"top\">";
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if($oQCocher->retDispQC() == 'Ver')
							$sHtmlListeObjForm .= "<table cellspacing=\"0\" cellpadding=\"0\">\n";
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
							{
								if($oQCocher->retDispQC() == 'Ver')
									$sHtmlListeObjForm.= "<tr><td><input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
											."value=\"".$oPropRep->retId()."\" /></td><td>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."</td></tr>\n";
								else
									$sHtmlListeObjForm .= "<input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
											."value=\"".$oPropRep->retId()."\" />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."\n";
							}
						}
						if($oQCocher->retDispQC() == 'Ver')
							$sHtmlListeObjForm .= "</table>\n";
						$sHtmlListeObjForm .= "</td>\n"
											."<td valign=\"top\">".$oQCocher->retTxtApQC()."</td>\n"
											."</tr></table>\n"
											."</div>\n";
						break;
				
				case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "<div align=\"".$oMPTexte->retAlignMPT()."\">".convertBaliseMetaVersHtml($oMPTexte->retTexteMPT())."</div>";
						break;
				
				case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
						$sHtmlListeObjForm .= "<hr width=\"".$oMPSeparateur->retLargeurCompleteMPS()."\" size=\"2\" align=\"".$oMPSeparateur->retAlignMPS()."\" />";
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
