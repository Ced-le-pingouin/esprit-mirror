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

class CQCocher
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;
	
	function CQCocher(&$v_oBdd, $v_iId = 0) 
	{
		$this->oBdd = &$v_oBdd;
			//si 0 crée un objet presque vide sinon 
			//rempli l'objet avec les données de la table QRadio
			//de l'elément ayant l'Id passé en argument 
			//(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QCocher WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type checkbox,
				 // avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QCocher SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/*
	** Fonction 		: RetourReponseQC
	** Description	: renvoie le code html contenant les checkbox avec les réponses,
	**					  si $v_iIdFC est présent la/les réponse(s) fournie(s) par l'étudiant sera/seront pré-sélectionnée	
	** Entrée			:
	**				$NbRepMaxQCTemp : nombre de réponses que l'étudiant peut cocher au maximum
	**				$MessMaxQCTemp : message personnalisé si l'étudiant coche trop de cases
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/
	function RetourReponseQC($NbRepMaxQCTemp, $MessMaxQCTemp, $v_iIdFC = NULL)
	{
		$TabRepEtu = array();
		if ($v_iIdFC != NULL)
		{
			//Sélection de la réponse donnée par l'étudiant
			$sRequeteSql = "SELECT * FROM ReponseEntier"
							." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			
			$i=0;
			$TabRepEtu=array();
			
			while ($oEnregRep = $this->oBdd->retEnregSuiv($hResultRep))
			{
				$TabRepEtu[$i] = $oEnregRep->IdReponse;
				//echo "<br>La réponse ".$TabRepEtu[$i];
				$i++;
			}
		}

		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
		$hResultRRQC = $this->oBdd->executerRequete($sRequeteSql);
		
		
		if ($this->oEnregBdd->DispQC == 'Ver')  //Présentation sous forme de tableau
		{
			$CodeHtml="<TABLE cellspacing=\"0\" cellpadding=\"0\">";

			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQC))
			{
				$oReponse = new CReponse($this->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				$IdObjFormTemp = $IdObjFormTemp."[]"; //utilise un tableau pour stocker les differents résultats possibles
				
				if(in_array($IdReponseTemp, $TabRepEtu))
					{$sPreSelection = "CHECKED";}
				else
					{$sPreSelection = "";}
				
				$CodeHtml.= "<TR><TD><INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
					."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection></TD><TD>$TexteTemp</TD></TR>\n";
			}
			$CodeHtml.="</TABLE>";
		}
		else //Présentation en ligne
		{
			$CodeHtml="";
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQC))
			{
				$oReponse = new CReponse($this->oBdd);
				//$oReponse = new CReponse($oCBdd->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				$IdObjFormTemp = $IdObjFormTemp."[]"; //utilise un tableau pour stocker les differents résultats possibles
				
				if (in_array($IdReponseTemp, $TabRepEtu))
					{$sPreSelection = "CHECKED";}
				else
					{$sPreSelection = "";}
				
				$CodeHtml .= "<INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
					."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection>$TexteTemp\n";
			}
		}
		
		$this->oBdd->libererResult($hResultRRQC);
		return "$CodeHtml";
	}
	
	/*
	** Fonction 		: cHtmlQCocher
	** Description	: renvoie le code html qui permet d'afficher une question de type case à cocher,
	**				     si $v_iIdFC est passé en paramètre il est envoyé à la fonction RetourReponseQC qui permettra
	**					  de pré-sélectionner la/les réponse(s) encodée(s) par l'étudiant
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété
	** Sortie			:
	**				code html
	*/
	function cHtmlQCocher($v_iIdFC = NULL)
	{
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQC = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQC);
		
		//Si alignement vertical alors suppression des textes avant et après sinon mise en forme
		if ($this->oEnregBdd->DispQC == 'Ver')
		{
			$this->oEnregBdd->TxtAvQC = "";
			$this->oEnregBdd->TxtApQC = "";
		}
		else
		{
			$this->oEnregBdd->TxtAvQC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQC);
			$this->oEnregBdd->TxtApQC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQC);
		}
		
		//Genération du code html représentant l'objet
		$sCodeHtml = "\n<!--QCocher : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQC}>{$this->oEnregBdd->EnonQC}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQC}>\n"
			."{$this->oEnregBdd->TxtAvQC} \n"
			//Appel de la fonction qui renvoie les réponses sous forme de cases à cocher,
			//avec la réponse sélectionnée par l'étudiant si IdFC est présent
			.$this->RetourReponseQC($this->oEnregBdd->NbRepMaxQC,$this->oEnregBdd->MessMaxQC,$v_iIdFC)
			." {$this->oEnregBdd->TxtApQC}\n"
			."</div>\n";
		
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
	
	function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}' ORDER BY OrdreReponse";
		
		$hResultRRQCM = $this->oBdd->executerRequete($sRequeteSql);
		
		$sCodeHtml="";
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQCM))
		{
			$oReponse = new CReponse($this->oBdd);
			$oReponse->init($oEnreg);
			
			//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
			$TexteTemp = $oReponse->retTexteReponse();
			$IdReponseTemp = $oReponse->retId();
			$IdObjFormTemp = $oReponse->retIdObjForm();
			
			if ($sCodeHtml != "")
				$sCodeHtml.="<tr>\n<td>\n&nbsp;\n</td>\n";

			$sCodeHtml.="<td>\n <input type=\"text\" size=\"70\" maxlength=\"255\" "
				."name=\"rep[$IdReponseTemp]\" value=\"".htmlentities($TexteTemp,ENT_COMPAT,"UTF-8")."\" />\n"
				."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br /></td></tr>\n"
				.RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); 
				//cette fc se trouve dans le fichier fonctions_form.inc.php
		}
		if(strlen($sCodeHtml)==0)
			$sCodeHtml = "<td>\n&nbsp;\n</td>\n</tr>\n";
		$this->oBdd->libererResult($hResultRRQCM);
		return $sCodeHtml;
	}

	
	function enregistrer ()
	{
		if ($this->oEnregBdd->IdObjForm !=NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
			$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
			$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
			
			$sRequeteSql =
				"  REPLACE QCocher SET"
				." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
				." , EnonQC='{$sEnonQC}'"
				." , AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
				." , AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
				." , TxtAvQC='{$sTxtAvQC}'"
				." , TxtApQC='{$sTxtApQC}'"
				." , DispQC='{$this->oEnregBdd->DispQC}'"
				." , NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
				." , MessMaxQC='{$this->oEnregBdd->MessMaxQC}'"
				;
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function copier ($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
		$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
		$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
		
		$sRequeteSql =
			"  INSERT INTO QCocher SET"
			." IdObjForm='{$v_iIdNvObjForm}'"
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
	
	function enregistrerRep ($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQC)
	{
		if ($v_iIdObjForm !=NULL)
		{
			$sRequeteSql =
				" INSERT INTO ReponseEntier SET"
				." IdFC='{$v_iIdFC}'"
				." , IdObjForm='{$v_iIdObjForm}'"
				." , IdReponse='{$v_sReponsePersQC}'";
			
			//echo "<br>enregistrer ReponsePersQC : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL: enregistrement impossible";
		}
	}
	
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM QCocher WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer QCocher()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQC ($v_sEnonQC) { $this->oEnregBdd->EnonQC = $v_sEnonQC; }
	function defAlignEnonQC ($v_sAlignEnonQC) { $this->oEnregBdd->AlignEnonQC = $v_sAlignEnonQC; }
	function defAlignRepQC ($v_sAlignRepQC) { $this->oEnregBdd->AlignRepQC = $v_sAlignRepQC; }
	function defTxtAvQC ($v_sTxtAvQC) { $this->oEnregBdd->TxtAvQC = $v_sTxtAvQC; }
	function defTxtApQC ($v_sTxtApQC) { $this->oEnregBdd->TxtApQC = $v_sTxtApQC; }
	function defDispQC ($v_sDispQC) { $this->oEnregBdd->DispQC = $v_sDispQC; }
	function defNbRepMaxQC ($v_iNbRepMaxQC) { $this->oEnregBdd->NbRepMaxQC = $v_iNbRepMaxQC; }
	function defMessMaxQC ($v_sMessMaxQC) { $this->oEnregBdd->MessMaxQC = $v_sMessMaxQC; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdReponse; }
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
