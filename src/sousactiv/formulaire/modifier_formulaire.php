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
$oTpl = new Template("modifier_formulaire.tpl");
$oBlockFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);
$oBlockFermer = new TPL_Block("BLOCK_FERMER",$oTpl);
$iIdUtilisateur = $oProjet->oUtilisateur->retId();

//	Récupération des variables
$v_iIdFormulaire = ( isset($_GET["idFormulaire"])?$_GET["idFormulaire"]:($_POST["idFormulaire"]?$_POST["idFormulaire"]:NULL) );
$iIdSousActiv = ( isset($_GET["idSousActiv"])?$_GET["idSousActiv"]:($_POST["idSousActiv"]?$_POST["idSousActiv"]:NULL) );

if(isset($_POST['bSoumis']))
{
	$bSoumis = TRUE;
	$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd);
	$oFormulaireComplete->verrouillerTables();
	$iIdFC = $oFormulaireComplete->ajouter($iIdUtilisateur, $v_iIdFormulaire);
	if(isset($iIdSousActiv))
	{
		$oSousActiv = new CSousActiv($oProjet->oBdd, $iIdSousActiv);
		list($sLien, $iMode, $sIntitule) = explode(";",$oSousActiv->retDonnees());
		if ($iMode == SOUMISSION_AUTOMATIQUE)
			$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_SOUMISE);
		else
			$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_EN_COURS);
	}
}
else
{
	$bSoumis = FALSE;
	if(isset($_GET["idFC"]))
	{
		$iIdFC = $_GET["idFC"];
		$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd, $iIdFC);
		$v_iIdFormulaire = $oFormulaireComplete->retIdFormul();
	}
	else
	{
		$iIdFC = NULL;
	}
}

if($v_iIdFormulaire > 0)
{
	if($bSoumis)
		$oBlockFormulaire->effacer();
	else
		$oBlockFormulaire->afficher();
	// Lecture de la table formulaire pour y récupérer les données de mise en page
	$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
	$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
	$iInterElem = $oFormulaire->retInterElem();
	$iInterEnonRep = $oFormulaire->retInterEnonRep();
	$iIdPersForm = $oFormulaire->retIdPers();
	$iRemplirTout = ( $oFormulaire->retRemplirTout()?1:0 );
	$bAutoCorrection = ( $oFormulaire->retAutoCorrection()?true:false );
	if($oFormulaire->retEncadrer() == 1)				//Vérifie s'il faut encadrer le titre ou non et compose le code html
		$sEncadrer = "style=\"border:1px solid black;\"";
	else
		$sEncadrer = "";
	$iLargeur = $oFormulaire->retLargeur();
	if($oFormulaire->retTypeLarg() == "P") //Pourcentage ou pixel
		$sLargeur = $iLargeur."%";
	else
		$sLargeur = $iLargeur."px";
	$oTpl->remplacer("{sLargeur}",$sLargeur);
	$oTpl->remplacer("{iInterEnonRep}",$iInterEnonRep);
	$oTpl->remplacer("{iInterElem}",$iInterElem);
	if($oProjet->verifPermission("PERM_EVALUER_FORMULAIRE")) // si c'est pour évaluer, on ne voit pas le boutoun valider
		$oTpl->remplacer("{bouton_valider}","");
	else
		$oTpl->remplacer("{bouton_valider}","<input type=\"button\" value=\"Valider\" name=\"soumettre\" onclick=\"validerFormulaire($iRemplirTout);\" />");
		
	$oTpl->remplacer("{general_js_php}",dir_code_lib_ced("general.js.php", FALSE, FALSE));
	$oTpl->remplacer("{formulaire_js}",dir_theme_commun("js/formulaire.js"));
	$oTpl->remplacer("{sEncadrer}",$sEncadrer);
	$oTpl->remplacer("{sTitre}",$sTitre);
	
	$oTpl->remplacer("{iIdFormulaire}",$v_iIdFormulaire);
	if(!empty($iIdSousActiv))
		$oTpl->remplacer("{input_ss_activ}","<input type=\"hidden\" name=\"idSousActiv\" value=\"{$iIdSousActiv}\" />\n");
	else
		$oTpl->remplacer("{input_ss_activ}","");
	$aoObjetFormulaire = $oFormulaire->retListeObjetFormulaire();
	$sHtmlListeObjForm = "";
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		$iIdObjActuel = $oObjetFormulaire->retId();
		$sHtmlListeObjForm .= "<a name=\"ancre{$iIdObjActuel}\"></a>\n";
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						$oQTexteLong->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
					{
						if ($iIdFC != NULL)
							$sValeur = retReponseTexteLong($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						else
							$sValeur = "";
						$sHtmlListeObjForm .= "\n<!--QTexteLong : $iIdObjActuel -->\n"
											."<div align=\"".$oQTexteLong->retAlignEnonQTL()."\">".convertBaliseMetaVersHtml($oQTexteLong->retEnonQTL())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQTexteLong->retAlignRepQTL()."\">\n"
											."<textarea name=\"$iIdObjActuel\" rows=\"".$oQTexteLong->retHauteurQTL()."\" cols=\"".$oQTexteLong->retLargeurQTL()."\">\n"
											."$sValeur</textarea>\n"
											."</div><br />\n";
					}
					break;
			
			case 2:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						$oQTexteCourt->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
					{
						if ($iIdFC != NULL)
							$sValeur = retReponseTexteCourt($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						else
							$sValeur = "";
						$sHtmlListeObjForm .= "\n<!--QTexteCourt : $iIdObjActuel -->\n"
											."<div align=\"".$oQTexteCourt->retAlignEnonQTC()."\">".convertBaliseMetaVersHtml($oQTexteCourt->retEnonQTC())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQTexteCourt->retAlignRepQTC()."\">\n"
											.convertBaliseMetaVersHtml($oQTexteCourt->retTxtAvQTC())
											."<input type=\"text\" name=\"$iIdObjActuel\" size=\"".$oQTexteCourt->retLargeurQTC()."\" maxlength=\"".$oQTexteCourt->retMaxCarQTC()."\" value=\"$sValeur\" />\n"
											.convertBaliseMetaVersHtml($oQTexteCourt->retTxtApQTC())
											."</div><br />\n";
					}
					break;
			
			case 3:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						// Transforme la virgule en point ex: 20,5 -> 20.5
						$_POST[$iIdObjActuel] = str_replace(",", ".", $_POST[$iIdObjActuel]);
						$oQNombre->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
					{
						if ($iIdFC != NULL)
							$sValeur = retReponseFlottant($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						else
							$sValeur = "";
						$sHtmlListeObjForm .= "\n<!--QNombre : $iIdObjActuel -->\n"
											."<div align=\"".$oQNombre->retAlignEnonQN()."\">".convertBaliseMetaVersHtml($oQNombre->retEnonQN())."</div>"
											."<div class=\"InterER\" align=\"".$oQNombre->retAlignRepQN()."\">"
											.convertBaliseMetaVersHtml($oQNombre->retTxTAvQN())
											."<input type=\"text\" name=\"$iIdObjActuel\" size=\"10\" maxlength=\"10\" value=\"$sValeur\""
											." id=\"id_".$oQNombre->retId()."_".$oQNombre->retNbMinQN()."_".$oQNombre->retNbMaxQN()."\" onchange=\"validerQNombre(this);\" />"
											.convertBaliseMetaVersHtml($oQNombre->retTxtApQN())
											."</div><br />\n";
					}
					break;
			
			case 4:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						$oQListeDeroul->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
					{
						$sHtmlListeObjForm .= "\n<!--QListeDeroul : $iIdObjActuel -->\n"
											."<div align=\"".$oQListeDeroul->retAlignEnonQLD()."\">".convertBaliseMetaVersHtml($oQListeDeroul->retEnonQLD())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQListeDeroul->retAlignRepQLD()."\">\n"
											.convertBaliseMetaVersHtml($oQListeDeroul->retTxTAvQLD());
						if($iIdFC != NULL)
							$iIdReponseEtu = retReponseEntier($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						else
							$iIdReponseEtu[0] = 0;
						$sHtmlListeObjForm .= "<select name=\"$iIdObjActuel\">\n";
						$sAutoCorr = "";
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
							{
								if($iIdReponseEtu[0] == $oPropRep->retId()) 
								{
									$sPreSelection = "selected=\"selected\"";
									if($bAutoCorrection)
									{
										switch($oPropRep->retScorePropRep())
										{
											case "-1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "0" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
										}
									}
								}
								else
								{
									$sPreSelection = "";
								}
								$sHtmlListeObjForm .= "<option value=\"".$oPropRep->retId()."\" $sPreSelection>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."</option>\n";
							}
						}
						$sHtmlListeObjForm .= "</select>\n".$sAutoCorr;
						$sHtmlListeObjForm .= convertBaliseMetaVersHtml($oQListeDeroul->retTxtApQLD())
											."</div>\n";
					}
					break;
			
			case 5:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						$oQRadio->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
					{
						$sHtmlListeObjForm .= "\n<!--QRadio : $iIdObjActuel -->\n"
											."<div align=\"".$oQRadio->retAlignEnonQR()."\">".convertBaliseMetaVersHtml($oQRadio->retEnonQR())."</div>\n"
											."<div class=\"InterER\" align=\"".$oQRadio->retAlignRepQR()."\">\n"
											."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>\n"
											."<td valign=\"top\">".convertBaliseMetaVersHtml($oQRadio->retTxTAvQR())."</td>\n"
											."<td valign=\"top\">";
						if($iIdFC != NULL)
							$iIdReponseEtu = retReponseEntier($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						else
							$iIdReponseEtu[0] = 0;
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if($oQRadio->retDispQR() == 'Ver')
							$sHtmlListeObjForm .= "<table cellspacing=\"0\" cellpadding=\"0\">";
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
							{
								$sAutoCorr = "";
								if($iIdReponseEtu[0] == $oPropRep->retId()) 
								{
									$sPreSelection = "checked=\"checked\"";
									if($bAutoCorrection)
									{
										switch($oPropRep->retScorePropRep())
										{
											case "-1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "0" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
										}
									}
								}
								else
								{
									$sPreSelection = "";
								}
								if($oQRadio->retDispQR() == 'Ver')
									$sHtmlListeObjForm .= "<tr><td><input type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" "
											."value=\"".$oPropRep->retId()."\" $sPreSelection /></td><td>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr</td></tr>\n";
								else
									$sHtmlListeObjForm .= "<input type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" value=\"".$oPropRep->retId()."\" $sPreSelection />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr \n";
							}
						}
						if($oQRadio->retDispQR() == 'Ver')
							$sHtmlListeObjForm .= "</table>";
						$sHtmlListeObjForm .= "</td>\n"
											."<td valign=\"top\">".convertBaliseMetaVersHtml($oQRadio->retTxtApQR())."</td>\n"
											."</tr></table>\n"
											."</div>\n";
					}
					break;
			
			case 6:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					if($bSoumis)
					{
						for ($i = 0; $i < count($_POST[$iIdObjActuel]); $i++) 
						{
							$oQCocher->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel][$i]);
						}
					}
					else
					{
						$sHtmlListeObjForm .= "\n<!--QCocher : $iIdObjActuel -->\n"
											."<div align=\"".$oQCocher->retAlignEnonQC()."\">".$oQCocher->retEnonQC()."</div>\n"
											."<div class=\"InterER\" align=\"".$oQCocher->retAlignEnonQC()."\">\n"
											."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>\n"
											."<td valign=\"top\">".$oQCocher->retTxTAvQC()."</td>\n"
											."<td valign=\"top\">";
						$TabRepEtu = array();
						if($iIdFC != NULL)
							$TabRepEtu = retReponseEntier($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
						$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
						if($oQCocher->retDispQC() == 'Ver')
							$sHtmlListeObjForm .= "<table cellspacing=\"0\" cellpadding=\"0\">\n";
						if(!empty($aoListePropRep))
						{
							foreach($aoListePropRep AS $oPropRep)
							{
								$sAutoCorr = "";
								if(in_array($oPropRep->retId(), $TabRepEtu))
								{
									$sPreSelection = "checked=\"checked\"";
									if($bAutoCorrection)
									{
										switch($oPropRep->retScorePropRep())
										{
											case "-1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "0" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
											case "1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
														break;
										}
									}
								}
								else
								{
									$sPreSelection = "";
									if($bAutoCorrection && $iIdFC!=NULL)
									{
										if($oPropRep->retScorePropRep() == 1)
											$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
									}
								}
								if($oQCocher->retDispQC() == 'Ver')
									$sHtmlListeObjForm.= "<tr><td><input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
											."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection /></td><td>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr</td></tr>\n";
								else
									$sHtmlListeObjForm .= "<input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
											."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr \n";
							}
						}
						if($oQCocher->retDispQC() == 'Ver')
							$sHtmlListeObjForm .= "</table>\n";
						$sHtmlListeObjForm .= "</td>\n"
											."<td valign=\"top\">".$oQCocher->retTxtApQC()."</td>\n"
											."</tr></table>\n"
											."</div>\n";
					}
					break;
			
			case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
					if(!$bSoumis)
						$sHtmlListeObjForm .= "<div align=\"".$oMPTexte->retAlignMPT()."\">".convertBaliseMetaVersHtml($oMPTexte->retTexteMPT())."</div>";
					break;
			
			case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
					if(!$bSoumis)
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
}
if($bSoumis)
{
	$oFormulaireComplete->deverrouillerTables();
	$oBlockFermer->afficher();
}
else
{
	$oBlockFermer->effacer();
}
$oTpl->afficher(); 
?>
