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
 * @file	qlistederoul.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "liste déroulante", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des questions de type "liste déroulante" des activités en ligne, et encapsulation de la table QListeDeroul de la DB
*/
class CQListeDeroul
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CQListeDeroul(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QListeDeroul WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type liste déroulante, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QListeDeroul SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/*
	** Fonction 		: RetourReponseQLDModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type liste déroulante en cours de traitement 
	**				  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/
	function RetourReponseQLDModif($v_iIdObjForm,$v_iIdFormulaire,$v_bAutoCorrection = false)
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
			$sEnonQLD = validerTexte($this->oEnregBdd->EnonQLD);
			$sTxtAvQLD = validerTexte($this->oEnregBdd->TxtAvQLD);
			$sTxtApQLD = validerTexte($this->oEnregBdd->TxtApQLD);
			
			$sRequeteSql = "REPLACE QListeDeroul SET"									  
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", EnonQLD='{$sEnonQLD}'"
						.", AlignEnonQLD='{$this->oEnregBdd->AlignEnonQLD}'"
						.", AlignRepQLD='{$this->oEnregBdd->AlignRepQLD}'"
						.", TxtAvQLD='{$sTxtAvQLD}'"
						.", TxtApQLD='{$sTxtApQLD}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function enregistrerRep($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQLD)
	{
		if ($v_iIdObjForm != NULL)
		{
			$sRequeteSql = "INSERT ReponseEntier SET"									  
						." IdFC='{$v_iIdFC}'"
						.", IdObjFormul='{$v_iIdObjForm}'"
						.", IdPropRep='{$v_sReponsePersQLD}'";
			
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
		$sEnonQLD = validerTexte($this->oEnregBdd->EnonQLD);
		$sTxtAvQLD = validerTexte($this->oEnregBdd->TxtAvQLD);
		$sTxtApQLD = validerTexte($this->oEnregBdd->TxtApQLD);
		
		$sRequeteSql = "INSERT INTO QListeDeroul SET"									  
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", EnonQLD='{$sEnonQLD}'"
					.", AlignEnonQLD='{$this->oEnregBdd->AlignEnonQLD}'"
					.", AlignRepQLD='{$this->oEnregBdd->AlignRepQLD}'"
					.", TxtAvQLD='{$sTxtAvQLD}'"
					.", TxtApQLD='{$sTxtApQLD}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QListeDeroul WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQLD ($v_sEnonQLD) { $this->oEnregBdd->EnonQLD = $v_sEnonQLD; }
	function defAlignEnonQLD ($v_sAlignEnonQLD) { $this->oEnregBdd->AlignEnonQLD = $v_sAlignEnonQLD; }
	function defAlignRepQLD ($v_sAlignRepQLD) { $this->oEnregBdd->AlignRepQLD = $v_sAlignRepQLD; }
	function defTxtAvQLD ($v_sTxtAvQLD) { $this->oEnregBdd->TxtAvQLD = $v_sTxtAvQLD; }
	function defTxtApQLD ($v_sTxtApQLD) { $this->oEnregBdd->TxtApQLD = $v_sTxtApQLD; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQLD () { return $this->oEnregBdd->EnonQLD; }
	function retAlignEnonQLD () { return $this->oEnregBdd->AlignEnonQLD; }
	function retAlignRepQLD () { return $this->oEnregBdd->AlignRepQLD; }
	function retTxTAvQLD () { return $this->oEnregBdd->TxtAvQLD; }
	function retTxtApQLD () { return $this->oEnregBdd->TxtApQLD; }
}
?>
