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
			$sRequeteSql = "SELECT * FROM QRadio WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type radio, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QRadio SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQR ($v_sEnonQR) { $this->oEnregBdd->EnonQR = $v_sEnonQR; }
	function defAlignEnonQR ($v_sAlignEnonQR) { $this->oEnregBdd->AlignEnonQR = $v_sAlignEnonQR; }
	function defAlignRepQR ($v_sAlignRepQR) { $this->oEnregBdd->AlignRepQR = $v_sAlignRepQR; }
	function defTxtAvQR ($v_sTxtAvQR) { $this->oEnregBdd->TxtAvQR = $v_sTxtAvQR; }	
	function defTxtApQR ($v_sTxtApQR) { $this->oEnregBdd->TxtApQR = $v_sTxtApQR; }	
	function defDispQR ($v_sDispQR) { $this->oEnregBdd->DispQR = $v_sDispQR; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
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
	function RetourReponseQR($v_iIdFC=NULL)
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
		
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
		$hResulRRQR = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oEnregBdd->DispQR == 'Ver')  //Présentation sous forme de tableau
		{
			$CodeHtml="<table cellspacing=\"0\" cellpadding=\"0\">";
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResulRRQR))
			{
				$oReponse = new CReponse($this->oBdd->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				
				if ($iIdReponseEtu == $IdReponseTemp) 
					$sPreSelection = "checked=\"checked\"";
				else 
					$sPreSelection = "";
				
				$CodeHtml.="<tr><td><input type=\"radio\" name=\"$IdObjFormTemp\" "
						."value=\"$IdReponseTemp\" $sPreSelection /></td><td>$TexteTemp</td></tr>\n";
			}
			$CodeHtml.="</table>";
		}
		else //Présentation en ligne
		{
			$CodeHtml="";
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResulRRQR))
			{
				$oReponse = new CReponse($this->oBdd->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				
				if ($iIdReponseEtu == $IdReponseTemp) 
					$sPreSelection = "checked=\"checked\"";
				else
					$sPreSelection = "";
				
				$CodeHtml .= "<input type=\"radio\" name=\"$IdObjFormTemp\" "
						."value=\"$IdReponseTemp\" $sPreSelection />$TexteTemp\n";
			}
		}
		$this->oBdd->libererResult($hResulRRQR);
		return $CodeHtml;
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
		$sCodeHtml = "\n<!--QRadio : {$this->oEnregBdd->IdObjForm} -->\n"
				."<div align={$this->oEnregBdd->AlignEnonQR}>{$this->oEnregBdd->EnonQR}</div>\n"
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
	function RetourReponseQRModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		/*
		Utilisation de l'objet CBdd bcp plus léger pour faire les requêtes qu'un objet Projet
		Attention ne pas oublier le : require_once (dir_database("bdd.class.php"));
		*/
		$oCBdd = new CBdd;
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}' ORDER BY OrdreReponse";
		$hResultInt = $oCBdd->executerRequete($sRequeteSql);
		
		$sCodeHtml="";
		
		while ($oEnreg = $oCBdd->retEnregSuiv($hResultInt))
		{	
			$oReponse = new CReponse($oCBdd->oBdd);
			$oReponse->init($oEnreg);
			
			//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
			$TexteTemp = $oReponse->retTexteReponse();
			$IdReponseTemp = $oReponse->retId();
			$IdObjFormTemp = $oReponse->retIdObjForm();
			
			//Ici(modif) la propriété name est l'Id de la réponse ce qui permet de les identifier pour les enregistrer
			//mais à l'affichage(liste) la propriété name est l'Id de l'objet ce qui permet d'avoir le meme nom pour
			//toutes les réponses et ainsi ne pas permettre de cocher +sieurs boutons radio
			
			if ($sCodeHtml != "")
				$sCodeHtml.="<tr>\n<td>\n&nbsp;\n</td>\n";
			
			$sCodeHtml.="<td>\n <input type=\"text\" size=\"70\" maxlength=\"255\" "
					."name=\"rep[$IdReponseTemp]\" value=\"".mb_convert_encoding($TexteTemp,"HTML-ENTITIES","UTF-8")."\" />\n"
					." <a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br /></td></tr>\n"
					.RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); //cette fc se trouve dans fonctions_form.inc.php
		} 
		if(strlen($sCodeHtml)==0)
			$sCodeHtml = "<td>\n&nbsp;\n</td>\n</tr>\n";
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjForm !=NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQR = validerTexte($this->oEnregBdd->EnonQR);
			$sTxtAvQR = validerTexte($this->oEnregBdd->TxtAvQR);
			$sTxtApQR = validerTexte($this->oEnregBdd->TxtApQR);
			
			$sRequeteSql = "REPLACE QRadio SET"
						." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
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
						.", IdObjForm='{$v_iIdObjForm}'"
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
					." IdObjForm='{$v_iIdNvObjForm}'"
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
		$sRequeteSql = "DELETE FROM QRadio WHERE IdObjForm ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
