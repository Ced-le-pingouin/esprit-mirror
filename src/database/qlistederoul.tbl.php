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
 * Contient la classe de gestion des questions de formulaire de type "nombre", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

class CQListeDeroul
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	function CQListeDeroul(&$v_oBdd,$v_iId=0) 
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
			$sRequeteSql = "SELECT * FROM QListeDeroul WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type liste déroulante, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QListeDeroul SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQLD ($v_sEnonQLD) { $this->oEnregBdd->EnonQLD = $v_sEnonQLD; }
	function defAlignEnonQLD ($v_sAlignEnonQLD) { $this->oEnregBdd->AlignEnonQLD = $v_sAlignEnonQLD; }
	function defAlignRepQLD ($v_sAlignRepQLD) { $this->oEnregBdd->AlignRepQLD = $v_sAlignRepQLD; }
	function defTxtAvQLD ($v_sTxtAvQLD) { $this->oEnregBdd->TxtAvQLD = $v_sTxtAvQLD; }
	function defTxtApQLD ($v_sTxtApQLD) { $this->oEnregBdd->TxtApQLD = $v_sTxtApQLD; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retEnonQLD () { return $this->oEnregBdd->EnonQLD; }
	function retAlignEnonQLD () { return $this->oEnregBdd->AlignEnonQLD; }
	function retAlignRepQLD () { return $this->oEnregBdd->AlignRepQLD; }
	function retTxTAvQLD () { return $this->oEnregBdd->TxtAvQLD; }
	function retTxtApQLD () { return $this->oEnregBdd->TxtApQLD; }
	
	/*
	** Fonction 		: RetourReponseQLD
	** Description	: renvoie le code html contenant la liste déroulante avec les réponses,
	**					  si $v_iIdFC la réponse fournie par l'étudiant sera pré-sélectionnée	
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/
	function RetourReponseQLD($v_iIdFC=NULL)
	{
		$iIdReponseEtu = "";
		if ($v_iIdFC != NULL)
		{
			//Sélection de la réponse donnée par l'étudiant
			$sRequeteSql = "SELECT * FROM ReponseEntier"
						." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->iId}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$iIdReponseEtu = $oEnregRep->IdReponse;
		}
		
		//Sélection de toutes les réponses concernant l'objet QListeDeroul en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
		$hResultRRQLD = $this->oBdd->executerRequete($sRequeteSql);
		
		$CodeHtml="<select name=\"{$this->iId}\">\n";
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQLD))
		{
			$oReponse = new CReponse($this->oBdd->oBdd);
			$oReponse->init($oEnreg);
			
			//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
			$TexteTemp = $oReponse->retTexteReponse();
			$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
			$IdReponseTemp = $oReponse->retId();
			$IdObjFormTemp = $oReponse->retIdObjForm();
			
			if ($iIdReponseEtu == $IdReponseTemp) 
				{$sPreSelection = "selected=\"selected\"";}
			else
				{$sPreSelection = "";}
			
			$CodeHtml .= "<option value=\"$IdReponseTemp\" $sPreSelection>$TexteTemp</option>\n";
		}
		$CodeHtml .= "</select>\n";
		
		$this->oBdd->libererResult($hResultRRQLD);
		return $CodeHtml;
	}
	
	/*
	** Fonction 		: cHtmlQListeDeroul
	** Description	: renvoie le code html qui permet d'afficher une question de type liste déroulante,
	**				     si $v_iIdFC est passé en paramètre il est envoyé à la fonction RetourReponseQLD qui permettra
	**					  de pré-sélectionner la réponse entrée par l'étudiant
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété
	** Sortie			:
	**				code html
	*/
	function cHtmlQListeDeroul($v_iIdFC=NULL)
	{
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQLD = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQLD);
		$this->oEnregBdd->TxtAvQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQLD);
		$this->oEnregBdd->TxtApQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQLD);
		
		//Genération du code html représentant l'objet
		$sCodeHtml = "\n<!--QListeDeroul : {$this->oEnregBdd->IdObjForm} -->\n"
					."<div align=\"{$this->oEnregBdd->AlignEnonQLD}\">{$this->oEnregBdd->EnonQLD}</div>\n"
					."<div class=\"InterER\" align=\"{$this->oEnregBdd->AlignRepQLD}\">\n"
					."{$this->oEnregBdd->TxtAvQLD} \n"
					.$this->RetourReponseQLD($v_iIdFC) 			//Appel de la fonction qui renvoie les réponses sous forme de liste déroulante, 
																//avec la réponse sélectionnée par l'étudiant si IdFC est présent
					." {$this->oEnregBdd->TxtApQLD}\n"
					."</div>\n";
		
		return $sCodeHtml;
	}
	
	/*
	** Fonction 		: RetourReponseQCModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type liste déroulante en cours de traitement 
	**				  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/
	function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
		$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
		$sCodeHtml = "";
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRep))
		{
			$oReponse = new CReponse($this->oBdd);
			$oReponse->init($oEnreg);
			
			//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
			$TexteTemp = $oReponse->retTexteReponse();
			$IdReponseTemp = $oReponse->retId();
			$IdObjFormTemp = $oReponse->retIdObjForm();
			
			if ($sCodeHtml != "")
				$sCodeHtml.="<tr>\n<td>\n&nbsp;\n</td>\n";
			
			$sCodeHtml.=" <td>\n <input type=\"text\" size=\"70\" maxlength=\"255\" "
						."name=\"rep[$IdReponseTemp]\" value=\"$TexteTemp\" />\n"
						."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a>\n<br />\n</td>\n</tr>\n"
						.RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); //cette fc se trouve dans fonctions_form.inc.php
		}
		if(strlen($sCodeHtml)==0)
			$sCodeHtml = "<td>\n&nbsp;\n</td>\n</tr>\n";
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjForm != NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQLD = validerTexte($this->oEnregBdd->EnonQLD);
			$sTxtAvQLD = validerTexte($this->oEnregBdd->TxtAvQLD);
			$sTxtApQLD = validerTexte($this->oEnregBdd->TxtApQLD);
			
			$sRequeteSql = "REPLACE QListeDeroul SET"									  
						." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
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
			$sRequeteSql = "REPLACE ReponseEntier SET"									  
						." IdFC='{$v_iIdFC}'"
						.", IdObjForm='{$v_iIdObjForm}'"
						.", IdReponse='{$v_sReponsePersQLD}'";
			
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
					." IdObjForm='{$v_iIdNvObjForm}'"
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
		$sRequeteSql = "DELETE FROM QListeDeroul WHERE IdObjForm ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
