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
 * @file	qcocher.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "case à cocher", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des questions de type "case à cocher" des activités en ligne, et encapsulation de la table QCocher de la DB
*/
class CQCocher
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CQCocher(&$v_oBdd, $v_iId = 0) 
	{
		$this->oBdd = &$v_oBdd;
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QCocher WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type checkbox, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QCocher SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/**
	 * Retourne le code HTML des propositions de réponses de question de type cases à cocher
	 * 
	 * @param	NbRepMaxQCTemp		nombre de réponses que l'étudiant peut cocher au maximum
	 * @param	MessMaxQCTemp 		message personnalisé si l'étudiant coche trop de cases
	 * @param	v_iIdFC				si l'id d'un formulaire complété est présent, la/les réponse(s) fournie(s) par l'étudiant sera/seront pré-sélectionnée
	 * @param	v_bAutoCorrection	si \c true, la question est de type auto-corrigée
	 * 
	 * @return	le code HTML des propositions de réponses de question de type cases à cocher
	 */
	function RetourReponseQC($NbRepMaxQCTemp, $MessMaxQCTemp, $v_iIdFC=NULL,$v_bAutoCorrection=true)
	{
		$TabRepEtu = array();
		if ($v_iIdFC != NULL)
			$TabRepEtu = retReponseEntier($this->oBdd,$v_iIdFC,$this->iId);
		
		$oPropositionReponse = new CPropositionReponse($this->oBdd);
		$aoListePropRep = $oPropositionReponse->retListePropRep($this->iId);
		if($this->retDispQC() == 'Ver')
			$sCodeHtml = "<table cellspacing=\"0\" cellpadding=\"0\">\n";
		else
			$sCodeHtml = "";
		
		if(!empty($aoListePropRep))
		{
			foreach($aoListePropRep AS $oPropRep)
			{
				$sAutoCorr = "";
				if(in_array($oPropRep->retId(), $TabRepEtu))
				{
					$sPreSelection = "checked=\"checked\"";
					if($v_bAutoCorrection)
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
					if($v_bAutoCorrection && $v_iIdFC!=NULL)
					{
						if($oPropRep->retScorePropRep()==1)
							$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropRep->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
					}
				}
				if($this->retDispQC() == 'Ver')
					$sCodeHtml.= "<tr><td><input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
							."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection /></td><td>".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr</td></tr>\n";
				else
					$sCodeHtml .= "<input type=\"checkbox\" name=\"".$oPropRep->retIdObjFormul()."[]\" "
							."value=\"".$oPropRep->retId()."\" onclick=\"verifNbQocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection />".convertBaliseMetaVersHtml($oPropRep->retTextePropRep())." $sAutoCorr \n";
			}
		}
		if($this->retDispQC() == 'Ver')
			$sCodeHtml .= "</table>\n";
		return $sCodeHtml;
	}
	
	/*
	** Fonction 		: RetourReponseQCModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type case à cocher en cours de traitement 
	**				  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/
	
	function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire,$v_bAutoCorrection = false)
	{
		$oPropositionReponse = new CPropositionReponse($this->oBdd);
		$iOrdreMax = $oPropositionReponse->retMaxOrdre($this->oEnregBdd->IdObjFormul);
		$aoListePropRep = $oPropositionReponse->retListePropRep($this->iId);		
		$sCodeHtml = "";
		if(!empty($aoListePropRep))
		{
			foreach($aoListePropRep AS $oPropRep)
			{
				// Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oPropRep->retTextePropRep();
				$IdReponseTemp = $oPropRep->retId();
				$IdObjFormTemp = $oPropRep->retIdObjFormul();
				$sFeedbackTemp = $oPropRep->retFeedbackPropRep();
				$iScoreTemp = $oPropRep->retScorePropRep();
				$iOrdreTemp = $oPropRep->retOrdre();
				
				// gestion pour selectionner le bon radio des scores
				switch($iScoreTemp)
				{
					case "-1" :	$sSelV = ""; $sSelX = "checked=\"checked\""; $sSelN = "";
								break;
					case "1" :	$sSelV = "checked=\"checked\""; $sSelX = ""; $sSelN = "";
								break;
					default :	$sSelV = ""; $sSelX = ""; $sSelN = "checked=\"checked\"";
				}
				
				// Entre chaque proposition de réponse, il faut mettre une ligne de séparation
				if ($sCodeHtml != "")
					$sCodeHtml.="<hr class=\"sepproprep\" />";
				
				// gestion du numéro d'ordre des propositions
				$sCodeOptionsOrdre = "";
				for ($iNumOrdre = 1; $iNumOrdre <= $iOrdreMax; $iNumOrdre++)
				{
					if($iNumOrdre == $iOrdreTemp)
						$sCodeOptionsOrdre .= "<option value=\"$iNumOrdre\" selected=\"selected\">$iNumOrdre</option>";
					else
						$sCodeOptionsOrdre .= "<option value=\"$iNumOrdre\">$iNumOrdre</option>";
				}
				
				$sCodeHtml.= "<div> Proposition ".$iOrdreTemp.": ";
				$sCodeHtml.= "\n <input type=\"text\" size=\"60\" maxlength=\"255\" name=\"rep[$IdReponseTemp]\" value=\"".emb_htmlentities($TexteTemp)."\" />\n";
				$sCodeHtml.= "<select name=\"selOrdreProposition[$IdReponseTemp]\">".$sCodeOptionsOrdre."</select>";
				if($v_bAutoCorrection)
				{
					$sCodeHtml.= "<span class=\"scores\">&nbsp;<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" /><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"1\" $sSelV />&nbsp;&nbsp;"
								."&nbsp;<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" /><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"-1\" $sSelX />&nbsp;&nbsp;"
								."&nbsp;<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" /><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"0\" $sSelN /></span>";
				}
				$sCodeHtml.="</div>";
				if($v_bAutoCorrection)
				{
					$sCodeHtml.="<div class=\"feedback\"><textarea cols=\"50\" rows=\"2\" name=\"feedbackRep[$IdReponseTemp]\" />$sFeedbackTemp</textarea></div>";
				}
				$sCodeHtml.= RetourPoidsReponse($this->oBdd,$v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); //cette fc se trouve dans fonctions_form.inc.php
				$sCodeHtml.= "<div align=\"right\"> <a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a> - <a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a> </div>\n";
			}
		}
		else
		{
			$sCodeHtml = "<div><a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a></div>\n";
		}
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
			$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
			$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
			
			$sRequeteSql = "REPLACE QCocher SET"
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						." , EnonQC='{$sEnonQC}'"
						." , AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
						." , AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
						." , TxtAvQC='{$sTxtAvQC}'"
						." , TxtApQC='{$sTxtApQC}'"
						." , DispQC='{$this->oEnregBdd->DispQC}'"
						." , NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
						." , MessMaxQC='{$this->oEnregBdd->MessMaxQC}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function copier($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
		$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
		$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
		
		$sRequeteSql = "INSERT INTO QCocher SET"
					." IdObjFormul='{$v_iIdNvObjForm}'"
					." , EnonQC='{$sEnonQC}'"
					." , AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
					." , AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
					." , TxtAvQC='{$sTxtAvQC}'"
					." , TxtApQC='{$sTxtApQC}'"
					." , DispQC='{$this->oEnregBdd->DispQC}'"
					." , NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
					." , MessMaxQC='{$this->oEnregBdd->MessMaxQC}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function enregistrerRep($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQC)
	{
		if ($v_iIdObjForm != NULL)
		{
			$sRequeteSql = " INSERT INTO ReponseEntier SET"
						." IdFC='{$v_iIdFC}'"
						." , IdObjFormul='{$v_iIdObjForm}'"
						." , IdPropRep='{$v_sReponsePersQC}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL: enregistrement impossible";
		}
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QCocher WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQC ($v_sEnonQC) { $this->oEnregBdd->EnonQC = $v_sEnonQC; }
	function defAlignEnonQC ($v_sAlignEnonQC) { $this->oEnregBdd->AlignEnonQC = $v_sAlignEnonQC; }
	function defAlignRepQC ($v_sAlignRepQC) { $this->oEnregBdd->AlignRepQC = $v_sAlignRepQC; }
	function defTxtAvQC ($v_sTxtAvQC) { $this->oEnregBdd->TxtAvQC = $v_sTxtAvQC; }
	function defTxtApQC ($v_sTxtApQC) { $this->oEnregBdd->TxtApQC = $v_sTxtApQC; }
	function defDispQC ($v_sDispQC) { $this->oEnregBdd->DispQC = $v_sDispQC; }
	function defNbRepMaxQC ($v_iNbRepMaxQC) { $this->oEnregBdd->NbRepMaxQC = $v_iNbRepMaxQC; }
	function defMessMaxQC ($v_sMessMaxQC) { $this->oEnregBdd->MessMaxQC = $v_sMessMaxQC; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQC () { return $this->oEnregBdd->EnonQC; }
	function retAlignEnonQC () { return $this->oEnregBdd->AlignEnonQC; }
	function retAlignRepQC () { return $this->oEnregBdd->AlignRepQC; }
	function retTxTAvQC () { return $this->oEnregBdd->TxtAvQC; }
	function retTxtApQC () { return $this->oEnregBdd->TxtApQC; }
	function retDispQC () { return $this->oEnregBdd->DispQC; }
	function retNbRepMaxQC () {return $this->oEnregBdd->NbRepMaxQC; }
	function retMessMaxQC () { return $this->oEnregBdd->MessMaxQC; }
}
?>
