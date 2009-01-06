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
$oBlockFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);	// block afficher tous les questions de l'AEL
$oBlockFermer = new TPL_Block("BLOCK_FERMER",$oTpl);			// block qui ferme la page
$oBlockEvalEtat = new TPL_Block("BLOCK_EVAL_ETAT",$oTpl);		// block pour afficher l'évaluation et l'état de l'AEL

/*
 * On v�rifie si la personne est visiteur ou connect�.
 */
if ($oProjet->retIdUtilisateur() > 0) $iIdUtilisateur = $oProjet->oUtilisateur->retId();
else $iIdUtilisateur = -1;

//	Récupération des variables
$v_iIdFormulaire = ( isset($_GET["idFormulaire"])?$_GET["idFormulaire"]:($_POST["idFormulaire"]?$_POST["idFormulaire"]:NULL) );
$iIdSousActiv = ( isset($_GET["idSousActiv"])?$_GET["idSousActiv"]:($_POST["idSousActiv"]?$_POST["idSousActiv"]:NULL) );
$iIdFC = ( isset($_GET["idFC"])?$_GET["idFC"]:NULL );

$bFormationArchivee = FALSE;
// si la formation est archiv�e et que l'utilisateur n'a pas les droits de modification
if ($oProjet->oFormationCourante->retStatut()== STATUT_ARCHIVE)
{
	$bFormationArchivee = TRUE; 
}

$bFermer = false;

if(isset($_POST['idFormulaire'])) // si le formulaire est soumis
{
	$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
	$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd);
	$oFormulaireComplete->verrouillerTables();
	$iIdFC = $oFormulaireComplete->ajouter($iIdUtilisateur, $v_iIdFormulaire);
	if(isset($iIdSousActiv))
	{
		$oSousActiv = new CSousActiv($oProjet->oBdd, $iIdSousActiv);
		list($sLien, $iMode, $sIntitule) = explode(";",$oSousActiv->retDonnees());
		if ($iMode == SOUMISSION_AUTOMATIQUE)
		{	// si full auto-corrigé, le statut sera directement ACCEPTEE
			if($oFormulaire->retAutoCorrection() && $oFormulaire->retNbreObjetFormulaireNonAutoCorrige()==0)
				$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_ACCEPTEE);
			else
				$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_SOUMISE);
		}
		else
		{
			$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_EN_COURS);
		}
	}
	$aoObjetFormulaire = $oFormulaire->retListeObjetFormulaire();
	if(!$oFormulaire->retAutoCorrection())
		$bFermer = true;
	// Enregistrer les réponses pour chaque éléments de l'AEL
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		$iIdObjActuel = $oObjetFormulaire->retId();
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
					$oQTexteLong->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					break;
			case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
					$oQTexteCourt->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					break;
			case 3:	$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
					// Transforme la virgule en point ex: 20,5 -> 20.5
					$_POST[$iIdObjActuel] = str_replace(",", ".", $_POST[$iIdObjActuel]);
					$oQNombre->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					break;
			case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
					$oQListeDeroul->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					break;
			case 5:	$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
					$oQRadio->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					break;
			case 6:	$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					for($i = 0; $i < count($_POST[$iIdObjActuel]); $i++) 
						$oQCocher->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel][$i]);
					break;
		}
	}
}
if(!$bFermer)
{
	if($iIdFC) // si l'AEL à déjà été complété
	{
		$oBlockEvalEtat->afficher();
		$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd,$iIdFC);
		$v_iIdFormulaire = $oFormulaireComplete->retIdFormul();
		$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
		$iIdPersEtudiant = $oFormulaireComplete->retIdPers();
		$oPersEtudiant = new CPersonne($oProjet->oBdd,$iIdPersEtudiant);
		$oTpl->remplacer("{Nom_etudiant}",$oPersEtudiant->retNom());
		$sInfoAEL = " (version ".$oFormulaireComplete->retTitre().")";
		if(isset($iIdSousActiv))
		{
			$bFullAutoCorr = false;
			$oFormulaireComplete_SousActiv = new CFormulaireComplete_SousActiv($oProjet->oBdd);
			$oFormulaireComplete_SousActiv->initParFcEtSsActiv($iIdFC,$iIdSousActiv);
			if($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_SOUMISE)
			{
				$sInfoAEL .= ", activité soumise le ".$oFormulaireComplete_SousActiv->retDate();
				$sEvalGlobale = "Evaluation globale de l'activité :";
				$oTpl->remplacer("{txt_eval}","L'activité n'a pas encore été évaluée par votre tuteur");
			}
			elseif($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_APPROF || $oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_ACCEPTEE)
			{
				$oFCE = new CFormulaireComplete_Evaluation($oProjet->oBdd);
				// initialisation de la dernière évaluation
				$oFCE->initPlusAncien($oFormulaireComplete_SousActiv->retIdFCSA());
				if($oFormulaire->retAutoCorrection() && $oFormulaire->retNbreObjetFormulaireNonAutoCorrige()==0)
				{
					$sInfoAEL .= ", activité réalisée le ".$oFormulaireComplete_SousActiv->retDate();
					$sEvalGlobale = "Remarques éventuelles du tuteur :";
					$bFullAutoCorr = true;
				}
				else
				{
					$oFormulaireComplete_Eval = new CFormulaireComplete_Evaluation($oProjet->oBdd,$oFormulaireComplete_SousActiv->retIdFCSA(),$oFormulaireComplete_SousActiv->retIdPers());
					$sInfoAEL .= ", activité évaluée le ".$oFormulaireComplete_Eval->retDate();
					$oFCE->initEvaluateur();
					$sEvalGlobale = "Evaluation globale de l'activité (".$oFCE->oEvaluateur->retNom().", le ".$oFormulaireComplete_Eval->retDate()."):";
				}
				if(strlen($oFCE->retAppreciation()) || strlen($oFCE->retCommentaire()))
					$oTpl->remplacer("{txt_eval}",convertBaliseMetaVersHtml($oFCE->retAppreciation())."<br />".convertBaliseMetaVersHtml($oFCE->retCommentaire()));
				else
					$oTpl->remplacer("{txt_eval}","Aucune remarque particulière n'a été communiquée par votre tuteur");
			}
		}
		$oTpl->remplacer("{Info_ael}",$sInfoAEL);
		$oTpl->remplacer("{Eval_Globale}",$sEvalGlobale);
		
	}
	else // AEL vide -> l'étudiant à cliquer sur le lien "Questionnaire de base à compléter"
	{
		$oBlockEvalEtat->effacer();
		$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
		$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd);
		/*
		 * $iIdUtilisateur est �gale � -1 si la personne est un visiteur.
		 */
		if ($iIdUtilisateur > -1)
		{
			$oTpl->remplacer("{Nom_etudiant}",$oProjet->oUtilisateur->retNom());
			$iNumVersion = 1 + $oFormulaireComplete->retNbreFormulaireComplete($iIdSousActiv,$iIdUtilisateur);
			$oTpl->remplacer("{Info_ael}"," (version $iNumVersion)");
		}
		else
		{
			$oTpl->remplacer("{Nom_etudiant}","visiteur");
			$iNumVersion = 1;
			$oTpl->remplacer("{Info_ael}"," (version $iNumVersion)");
		}
	}
}
if($v_iIdFormulaire && !$bFermer) // s'il y a une AEL
{
	$oBlockFormulaire->afficher();
	$oBlockFermer->effacer();
	$bAutoCorrection = ( $oFormulaire->retAutoCorrection()?true:false );
	// Lecture de la table formulaire pour y récupérer les données de mise en page
	$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
	$iInterElem = $oFormulaire->retInterElem();
	$iInterEnonRep = $oFormulaire->retInterEnonRep();
	$iIdPersForm = $oFormulaire->retIdPers();
	$iRemplirTout = ( $oFormulaire->retRemplirTout()?1:0 );
	
	if($oFormulaire->retEncadrer() == 1)	//Vérifie s'il faut encadrer le titre ou non et compose le code html
		$sEncadrer = "style=\"border:1px solid black;\"";
	else
		$sEncadrer = "";
	$iLargeur = $oFormulaire->retLargeur();
	if($oFormulaire->retTypeLarg() == "P")	//Pourcentage ou pixel
		$sLargeur = $iLargeur."%";
	else
		$sLargeur = $iLargeur."px";
	$oTpl->remplacer("{sLargeur}",$sLargeur);
	$oTpl->remplacer("{iInterEnonRep}",$iInterEnonRep);
	$oTpl->remplacer("{iInterElem}",$iInterElem);
	if($oProjet->verifPermission("PERM_EVALUER_FORMULAIRE") || isset($_POST['idFormulaire']) || $bFormationArchivee)
	{	// si c'est pour évaluer ou afficher les feedbacks de l'auto-correction, on ne voit pas le bouton valider
		$oTpl->remplacer("{bouton_valider}","&nbsp;");
		if(isset($_POST['idFormulaire']))
			$oTpl->remplacer("{bouton_fermer}","<a id=\"fermer\" href=\"javascript: top.opener.location=top.opener.location; top.close();\">Fermer</a>");
		else
			$oTpl->remplacer("{bouton_fermer}","<a id=\"fermer\" href=\"javascript: top.close();\">Fermer</a>");
	}
	else
	{
		$oTpl->remplacer("{bouton_valider}","<a id=\"soumettre\" href=\"javascript: validerFormulaire($iRemplirTout);\">Valider</a>");
		$oTpl->remplacer("{bouton_fermer}","<a id=\"fermer\" href=\"javascript: top.close();\">Fermer</a>");
	}
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
	$fScore = 0;
	$fScoreMax = 0;
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		$iIdObjActuel = $oObjetFormulaire->retId();
		$sHtmlListeObjForm .= "<a name=\"ancre{$iIdObjActuel}\"></a>\n";
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
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
					break;
			
			case 2:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
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
					break;
			
			case 3:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
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
					break;
			
			case 4:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
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
					$sFeedback = "";
					if(!empty($aoListePropRep))
					{
						$iNbrePropRep = $iNbrePropRepCorrecte = $iNbrePropRepFausse = 0;
						$iNbreRepCorrecte = $iNbreRepFausse = 0;
						foreach($aoListePropRep AS $oPropRep)
						{
							if($iIdReponseEtu[0] == $oPropRep->retId()) 
							{
								$sPreSelection = "selected=\"selected\"";
								if($bAutoCorrection)
								{
									switch($oPropRep->retScorePropRep())
									{
										case "-1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepFausse++;
													break;
										case "0" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													break;
										case "1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepCorrecte++;
													break;
									}
									$sFeedback = "<div id=\"FBT".$oPropRep->retId()."\" class=\"feedback_titre\"><p>Feedback spécifique</p></div>"
												."<div id=\"FB".$oPropRep->retId()."\" class=\"feedback\">"
												."<p>".(strlen($oPropRep->retFeedbackPropRep())?convertBaliseMetaVersHtml($oPropRep->retFeedbackPropRep()):"Aucun feedback spécifique")."</p></div>";
								}
							}
							else
							{
								$sPreSelection = "";
							}
							if($bAutoCorrection)
							{
								switch($oPropRep->retScorePropRep())
								{
									case "-1" :	$iNbrePropRepFausse++;
												break;
									case "1" :	$iNbrePropRepCorrecte++;
												break;
								}
								$iNbrePropRep++;
							}
							$sHtmlListeObjForm .= "<option value=\"".$oPropRep->retId()."\" $sPreSelection>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())."</option>\n";
						}
					}
					if($bAutoCorrection)
					{
						$fScore += CalculerScore(1,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse,$oFormulaire->retMethodeCorrection());
						$fScoreMax += 1;
					}
					$sHtmlListeObjForm .= "</select id=\"autocorrect\">\n".$sAutoCorr;
					$sHtmlListeObjForm .= convertBaliseMetaVersHtml($oQListeDeroul->retTxtApQLD())
										."</div>\n".$sFeedback."\n";
					break;
			
			case 5:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
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
					$sFeedback = "";
					if(!empty($aoListePropRep))
					{
						$iNbrePropRep = $iNbrePropRepCorrecte = $iNbrePropRepFausse = 0;
						$iNbreRepCorrecte = $iNbreRepFausse = 0;
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
										case "-1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepFausse++;
													break;
										case "0" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													break;
										case "1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().");\">"
																."<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepCorrecte++;
													break;
									}
									$sFeedback = "<div id=\"FBT".$oPropRep->retId()."\" class=\"feedback_titre\"><p>Feedback spécifique</p></div>"
												."<div id=\"FB".$oPropRep->retId()."\" class=\"feedback\">"
												."<p>".(strlen($oPropRep->retFeedbackPropRep())?convertBaliseMetaVersHtml($oPropRep->retFeedbackPropRep()):"Aucun feedback spécifique")."</p></div>";
								}
							}
							else
							{
								$sPreSelection = "";
							}
							if($bAutoCorrection)
							{
								switch($oPropRep->retScorePropRep())
								{
									case "-1" :	$iNbrePropRepFausse++;
												break;
									case "1" :	$iNbrePropRepCorrecte++;
												break;
								}
								$iNbrePropRep++;
							}
							if($oQRadio->retDispQR() == 'Ver')
								$sHtmlListeObjForm .= "<tr><td><input type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" "
										."value=\"".$oPropRep->retId()."\" $sPreSelection /></td><td id=\"autocorrect\">".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr</td></tr>\n";
							else
								$sHtmlListeObjForm .= "<input id=\"autocorrect\" type=\"radio\" name=\"".$oPropRep->retIdObjFormul()."\" value=\"".$oPropRep->retId()."\" $sPreSelection />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr \n";
						}
					}
					if($bAutoCorrection)
					{
						$fScore += CalculerScore(1,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse,$oFormulaire->retMethodeCorrection());
						$fScoreMax += 1;
					}
					if($oQRadio->retDispQR() == 'Ver')
						$sHtmlListeObjForm .= "</table>";
					$sHtmlListeObjForm .= "</td>\n"
										."<td valign=\"top\">".convertBaliseMetaVersHtml($oQRadio->retTxtApQR())."</td>\n"
										."</tr></table>\n"
										."</div>\n".$sFeedback."\n";
					break;
			
			case 6:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					$TabRepEtu = array();
					if($iIdFC != NULL)
					{
						$TabRepEtu = retReponseEntier($oProjet->oBdd,$iIdFC,$iIdObjActuel);
						$sRepEtud = ""; // utilisé pour créer un tableau en javascript
						foreach($TabRepEtu as $sTmp)
						{
							if( $sRepEtud != "")
								$sRepEtud .= ",'$sTmp'";
							else
								$sRepEtud = "'$sTmp'";
						}
					}
					$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
					$aoListePropRep = $oPropositionReponse->retListePropRep($iIdObjActuel);
					if($oQCocher->retDispQC() == 'Ver')
						$sPropRepQCocher = "<table cellspacing=\"0\" cellpadding=\"0\">\n";
					else
						$sPropRepQCocher = "";
					$sFeedback = "";
					if(!empty($aoListePropRep))
					{
						$iNbrePropRep = $iNbrePropRepCorrecte = $iNbrePropRepFausse = 0;
						$iNbreRepCorrecte = $iNbreRepFausse = 0;
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
										case "-1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().",new Array($sRepEtud));\">"
																."<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepFausse++;
													break;
										case "0" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().",new Array($sRepEtud));\">"
																."<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													break;
										case "1" :	$sAutoCorr = "<a href=\"javascript: GestionFeedback(".$oPropRep->retId().",new Array($sRepEtud));\">"
																."<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />"
																."</a>";
													$iNbreRepCorrecte++;
													break;
									}
									$sFeedback .= "<div id=\"FBT".$oPropRep->retId()."\" class=\"feedback_titre\"><p>Feedback spécifique</p></div>"
												."<div id=\"FB".$oPropRep->retId()."\" class=\"feedback\">"
												."<p>".(strlen($oPropRep->retFeedbackPropRep())?convertBaliseMetaVersHtml($oPropRep->retFeedbackPropRep()):"Aucun feedback spécifique")."</p></div><br style=\"clear: both; display: none;\" />\n";
								}
							}
							else
							{
								$sPreSelection = "";
							}
							if($bAutoCorrection)
							{
								switch($oPropRep->retScorePropRep())
								{
									case "-1" :	$iNbrePropRepFausse++;
												break;
									case "1" :	$iNbrePropRepCorrecte++;
												break;
								}
								$iNbrePropRep++;
							}
							if($oQCocher->retDispQC() == 'Ver')
								$sPropRepQCocher.= "<tr><td><input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
										."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection /></td><td id=\"autocorrect\">".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr</td></tr>\n";
							else
								$sPropRepQCocher .= "<input id=\"autocorrect\" type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
										."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr \n";
						}
					}
					if($bAutoCorrection)
					{
						$fScore += CalculerScore($iNbrePropRepCorrecte,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse,$oFormulaire->retMethodeCorrection());
						$fScoreMax += 1;
					}
					if($oQCocher->retDispQC() == 'Ver')
						$sPropRepQCocher .= "</table>\n";
					if($iNbrePropRepCorrecte!=$iNbreRepCorrecte && $iNbreRepCorrecte>0)
						$sIncomplet = "<img src=\"".dir_theme_commun('icones/incomplet.gif')."\" align=\"top\" alt=\"Réponse incomplète\" title=\"Réponse incomplète\" />";
					else
						$sIncomplet = "";
					$sHtmlListeObjForm .= "\n<!--QCocher : $iIdObjActuel -->\n"
										."<div align=\"".$oQCocher->retAlignEnonQC()."\">".convertBaliseMetaVersHtml($oQCocher->retEnonQC()).$sIncomplet."</div>\n"
										."<div class=\"InterER\" align=\"".$oQCocher->retAlignEnonQC()."\">\n"
										."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>\n"
										."<td valign=\"top\">".$oQCocher->retTxTAvQC()."</td>\n"
										."<td valign=\"top\">";
					$sHtmlListeObjForm .= $sPropRepQCocher;
					$sHtmlListeObjForm .= "</td>\n"
										."<td valign=\"top\">".$oQCocher->retTxtApQC()."</td>\n"
										."</tr></table>\n"
										."</div>\n".$sFeedback."\n";
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
	$sEtat = "<table id=\"tab_etat\"><tr>";
	if($bAutoCorrection && $iIdFC!=NULL) // si score
	{
		$iPourcentage = 0;
		if($fScore > 0)
			$iPourcentage = round(($fScore/$fScoreMax)*100);
		if($bFullAutoCorr)
		{
			$sEtat .= "<td class=\"separvert\">L'activité a été réalisée</td>";
			$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"évalué\"/></td></tr>";
			$sEtat .= "<tr><td class=\"separvert\">Le score obtenu est de :</td>";
			$sEtat .= "<td class=\"statut_ael\">$iPourcentage% </td>";
			$sEtat .= "</tr></table>";
		}
		else
		{
			$sEtat .= "<td class=\"separvert\">1</td>";
			$sEtat .= "<td class=\"separvert\">L'activité a fait l'objet d'une première évaluation (auto correction)</td>";
			$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"évalué\" /></td></tr>";
			$sEtat .= "<tr><td class=\"separvert\">&nbsp;</td>";
			$sEtat .= "<td class=\"separvert\">Le score obtenu est de :</td>";
			$sEtat .= "<td class=\"statut_ael\">$iPourcentage% </td>";
			if($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_SOUMISE)
			{
				$sEtat .= "<tr class=\"separhori\"><td class=\"separvert\">2</td>";
				$sEtat .= "<td class=\"separvert\">L'activité n'a pas encore été évaluée par votre tuteur</td>";
				$sEtat .= "<td class=\"statut_ael\"><img src=\"theme://formulaire/res_non_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"non-évalué\" /></td>";
			}
			else
			{
				$sEtat .= "<tr class=\"separhori\"><td class=\"separvert\">2</td>";
				$sEtat .= "<td class=\"separvert\">L'activité a été évalué par votre tuteur</td>";
				$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"évalué\"/></td></tr>";
				if($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_APPROF)
				{
					$sEtat .= "<tr><td class=\"separvert\">&nbsp;</td>";
					$sEtat .= "<td class=\"separvert\">Il vous demande de la poursuivre</td>";
					$sEtat .= "<td><img src=\"theme://formulaire/res_a_poursuivre.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"à poursuivre\"/></td></tr>";
				}
				elseif($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_ACCEPTEE)
				{
					$sEtat .= "<tr><td class=\"separvert\">&nbsp;</td>";
					$sEtat .= "<td class=\"separvert\">L'activité est terminée</td>";
					$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"terminée\"/></td></tr>";
				}
			}
		}
	}
	elseif($iIdFC!=NULL) // si AEL sans scores
	{
		if($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_SOUMISE)
		{
			$sEtat .= "<td class=\"separvert\">L'activité n'a pas encore été évaluée par votre tuteur</td>";
			$sEtat .= "<td class=\"statut_ael\"><img src=\"theme://formulaire/res_non_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"non-évalué\" /></td>";
		}
		else
		{
			$sEtat .= "<td class=\"separvert\">L'activité a été évaluée par votre tuteur</td>";
			$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"évalué\"/></td></tr>";
			if($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_APPROF)
			{
				$sEtat .= "<tr class=\"separhori\"><td class=\"separvert\">Il vous demande de la poursuivre</td>";
				$sEtat .= "<td><img src=\"theme://formulaire/res_a_poursuivre.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"à poursuivre\"/></td></tr>";
			}
			elseif($oFormulaireComplete_SousActiv->retStatut() == STATUT_RES_ACCEPTEE)
			{
				$sEtat .= "<tr class=\"separhori\"><td class=\"separvert\">L'activité est terminée</td>";
				$sEtat .= "<td><img src=\"theme://formulaire/res_eval.gif\" width=\"8\" height=\"8\" border=\"0\" alt=\"terminée\"/></td></tr>";
			}
		}
	}
	$sEtat .= "</tr></table>";
	$oTpl->remplacer("{txt_etat}",$sEtat);
}
else
{
	$oBlockFormulaire->effacer();
	$oBlockFermer->afficher();
}

$oTpl->afficher(); 
?>
