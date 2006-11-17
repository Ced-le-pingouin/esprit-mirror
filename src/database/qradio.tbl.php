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

require_once (dir_database("bdd.class.php"));  	//permet d'utiliser la bdd sans creer un objet
												//CProjet et ainsi cela permet de creer des objets
												//d'une autre classe à partir de celle-ci.

/**
 * @file	qradio.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "radio", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

class CQRadio
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	function CQRadio(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QRadio WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}

	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type radio, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QRadio SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQR ($v_sEnonQR) { $this->oEnregBdd->EnonQR = $v_sEnonQR; }
	function defAlignEnonQR ($v_sAlignEnonQR) { $this->oEnregBdd->AlignEnonQR = $v_sAlignEnonQR; }
	function defAlignRepQR ($v_sAlignRepQR) { $this->oEnregBdd->AlignRepQR = $v_sAlignRepQR; }
	function defTxtAvQR ($v_sTxtAvQR) { $this->oEnregBdd->TxtAvQR = $v_sTxtAvQR; }	
	function defTxtApQR ($v_sTxtApQR) { $this->oEnregBdd->TxtApQR = $v_sTxtApQR; }	
	function defDispQR ($v_sDispQR) { $this->oEnregBdd->DispQR = $v_sDispQR; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQR () { return $this->oEnregBdd->EnonQR; }
	function retAlignEnonQR () { return $this->oEnregBdd->AlignEnonQR; }
	function retAlignRepQR () { return $this->oEnregBdd->AlignRepQR; }
	function retTxTAvQR () { return $this->oEnregBdd->TxtAvQR; }
	function retTxtApQR () { return $this->oEnregBdd->TxtApQR; }
	function retDispQR () { return $this->oEnregBdd->DispQR; }
	
	/*
	** Fonction 		: RetourReponseQR
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type bouton Radio en cours de traitement 
	**				  + mise en page de ces réponses,
	**				  si $v_iIdFC la réponse fournie par l'étudiant sera pré-sélectionnée
	** Entrée			:
	**				  $v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			: Code Html
	*/
	function RetourReponseQR($v_iIdFC=NULL,$v_bAutoCorrection=true)
	{
		$iIdReponseEtu = "";
		if ($v_iIdFC != NULL)
		{
			//Sélection de la réponse donnée par l'étudiant
			$sRequeteSql = "SELECT IdReponse FROM ReponseEntier WHERE IdFC='{$v_iIdFC}' AND IdObjFormul='{$this->iId}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$iIdReponseEtu = $oEnregRep->IdReponse;
		}
		
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM PropositionReponse WHERE IdObjFormul = '{$this->iId}'"
					." ORDER BY OrdrePropRep";
		$hResulRRQR = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oEnregBdd->DispQR == 'Ver')  //Présentation sous forme de tableau
		{
			$sCodeHtml = "<table cellspacing=\"0\" cellpadding=\"0\">";
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResulRRQR))
			{
				$sAutoCorr = "";
				$oPropositionReponse = new CPropositionReponse($this->oBdd->oBdd);
				$oPropositionReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oPropositionReponse->retTextePropRep();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oPropositionReponse->retId();
				$IdObjFormTemp = $oPropositionReponse->retIdObjFormul();
				
				if ($iIdReponseEtu == $IdReponseTemp) 
				{
					$sPreSelection = "checked=\"checked\"";
					if($v_bAutoCorrection)
					{
						switch($oPropositionReponse->retScorePropRep())
						{
							case "-1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" alt=\"X\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
							
							case "0" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" alt=\"-\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
							
							case "1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" alt=\"V\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
						}
					}
				}
				else
				{
					$sPreSelection = "";
				}
				$sCodeHtml.="<tr><td><input type=\"radio\" name=\"$IdObjFormTemp\" "
						."value=\"$IdReponseTemp\" $sPreSelection /></td><td>$TexteTemp $sAutoCorr</td></tr>\n";
			}
			$sCodeHtml.="</table>";
		}
		else //Présentation en ligne
		{
			$sCodeHtml = "";
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResulRRQR))
			{
				$sAutoCorr = "";
				$oPropositionReponse = new CPropositionReponse($this->oBdd->oBdd);
				$oPropositionReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oPropositionReponse->retTextePropRep();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oPropositionReponse->retId();
				$IdObjFormTemp = $oPropositionReponse->retIdObjFormul();
				
				if ($iIdReponseEtu == $IdReponseTemp) 
				{
					$sPreSelection = "checked=\"checked\"";
					if($v_bAutoCorrection)
					{
						switch($oPropositionReponse->retScorePropRep())
						{
							case "-1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/x.gif')."\" align=\"top\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
							
							case "0" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/-.gif')."\" align=\"top\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
							
							case "1" :	$sAutoCorr = "<img src=\"".dir_theme_commun('icones/v.gif')."\" align=\"top\" title=\"".htmlspecialchars($oPropositionReponse->retFeedbackPropRep(),ENT_COMPAT,"UTF-8")."\" />";
										break;
						}
					}
				}
				else
				{
					$sPreSelection = "";
				}
				
				$sCodeHtml .= "<input type=\"radio\" name=\"$IdObjFormTemp\" value=\"$IdReponseTemp\" $sPreSelection />$TexteTemp $sAutoCorr \n";
			}
		}
		$this->oBdd->libererResult($hResulRRQR);
		return $sCodeHtml;
	}
	
	/*
	** Fonction 		: cHtmlQRadio
	** Description	: renvoie le code html qui permet d'afficher une question de type bouton radio,
	**				     si $v_iIdFC est passé en paramètre il est envoyé à la fonction RetourReponseQR qui permettra
	**					  de pré-sélectionner la réponse entrée par l'étudiant
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété
	** Sortie			:
	**				code html
	*/
	function cHtmlQRadio($v_iIdFC=NULL)
	{
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQR = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQR);
		
		$this->oEnregBdd->TxtAvQR = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQR);
		$this->oEnregBdd->TxtApQR = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQR);
		
		//Genération du code html représentant l'objet
		$sCodeHtml = "\n<!--QRadio : {$this->oEnregBdd->IdObjFormul} -->\n"
				."<div align=\"{$this->oEnregBdd->AlignEnonQR}\">{$this->oEnregBdd->EnonQR}</div>\n"
				."<div class=\"InterER\" align=\"{$this->oEnregBdd->AlignRepQR}\">\n"
				."<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\"><tr>"
				."<td valign=\"top\">"
					."{$this->oEnregBdd->TxtAvQR} \n"
				."</td>"
				// appel de la fonction qui renvoie les réponses sous forme de bouton radio,
				// avec la réponse cochée par l'étudiant si IdFC est présent
				."<td valign=\"top\">"
					.$this->RetourReponseQR($v_iIdFC)
				."</td>"
				."<td valign=\"top\">"
					." {$this->oEnregBdd->TxtApQR}\n"
				."</td>"
				."</tr></table>"
				."</div>\n";
		
		return $sCodeHtml;
	}
	
	/*
	** Fonction 		: RetourReponseQRModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type bouton Radio en cours de traitement 
	**				  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/
	function RetourReponseQRModif($v_iIdObjForm,$v_iIdFormulaire,$v_bAutoCorrection = false)
	{
		// Recherche du numéro d'ordre maximum
		$hResult = $this->oBdd->executerRequete("SELECT MAX(OrdrePropRep) AS OrdreMax FROM PropositionReponse WHERE IdObjFormul='{$this->oEnregBdd->IdObjFormul}'");
		$oEnreg = $this->oBdd->retEnregSuiv();
		$iOrdreMax = $oEnreg->OrdreMax;
		$this->oBdd->libererResult($hResult);

		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM PropositionReponse WHERE IdObjFormul = '{$this->iId}' ORDER BY OrdrePropRep";
		$hResultInt = $this->oBdd->executerRequete($sRequeteSql);
		
		$sCodeHtml = "";
		
		while($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
		{	
			$oPropositionReponse = new CPropositionReponse($this->oBdd);
			$oPropositionReponse->init($oEnreg);
			
			//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
			$TexteTemp = $oPropositionReponse->retTextePropRep();
			$IdReponseTemp = $oPropositionReponse->retId();
			$IdObjFormTemp = $oPropositionReponse->retIdObjFormul();
			$sFeedbackTemp = $oPropositionReponse->retFeedbackPropRep();
			$iScoreTemp = $oPropositionReponse->retScorePropRep();
			$iOrdreTemp = $oPropositionReponse->retOrdre();
			
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
		if(strlen($sCodeHtml)==0)
			$sCodeHtml = "<div><a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a></div>\n";
		$this->oBdd->libererResult($hResultInt);
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQR = validerTexte($this->oEnregBdd->EnonQR);
			$sTxtAvQR = validerTexte($this->oEnregBdd->TxtAvQR);
			$sTxtApQR = validerTexte($this->oEnregBdd->TxtApQR);
			
			$sRequeteSql = "REPLACE QRadio SET"
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", EnonQR='{$sEnonQR}'"
						.", AlignEnonQR='{$this->oEnregBdd->AlignEnonQR}'"
						.", AlignRepQR='{$this->oEnregBdd->AlignRepQR}'"
						.", TxtAvQR='{$sTxtAvQR}'"
						.", TxtApQR='{$sTxtApQR}'"
						.", DispQR='{$this->oEnregBdd->DispQR}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function enregistrerRep($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQR)
	{
		if ($v_iIdObjForm !=NULL)
		{
			$sRequeteSql = "REPLACE ReponseEntier SET"									  
						." IdFC='{$v_iIdFC}'"
						.", IdObjFormul='{$v_iIdObjForm}'"
						.", IdReponse='{$v_sReponsePersQR}'";
				
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
		$sEnonQR = validerTexte($this->oEnregBdd->EnonQR);
		$sTxtAvQR = validerTexte($this->oEnregBdd->TxtAvQR);
		$sTxtApQR = validerTexte($this->oEnregBdd->TxtApQR);
		
		$sRequeteSql = "INSERT INTO QRadio SET"
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", EnonQR='{$sEnonQR}'"
					.", AlignEnonQR='{$this->oEnregBdd->AlignEnonQR}'"
					.", AlignRepQR='{$this->oEnregBdd->AlignRepQR}'"
					.", TxtAvQR='{$sTxtAvQR}'"
					.", TxtApQR='{$sTxtApQR}'"
					.", DispQR='{$this->oEnregBdd->DispQR}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QRadio WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
