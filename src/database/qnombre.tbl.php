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
 * @file	qnombre.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "nombre", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des questions de type "nombre" des activités en ligne, et encapsulation de la table QNombre de la DB
*/
class CQNombre 
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CQNombre(&$v_oBdd,$v_iId=0) 
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
			$sRequeteSql = "SELECT * FROM QNombre WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type nombre, avec tous ses champs vides, en fin de table
	{
		$sRequeteSql = "INSERT INTO QNombre SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQN ($v_sEnonQN) { $this->oEnregBdd->EnonQN = $v_sEnonQN; }
	function defAlignEnonQN ($v_sAlignEnonQN) { $this->oEnregBdd->AlignEnonQN = $v_sAlignEnonQN; }
	function defAlignRepQN ($v_sAlignRepQN) { $this->oEnregBdd->AlignRepQN = $v_sAlignRepQN; }
	function defTxtAvQN ($v_sTxtAvQN) { $this->oEnregBdd->TxtAvQN = $v_sTxtAvQN; }
	function defTxtApQN ($v_sTxtApQN) { $this->oEnregBdd->TxtApQN = $v_sTxtApQN; }
	function defNbMinQN ($v_iNbMinQN) { $this->oEnregBdd->NbMinQN = trim($v_iNbMinQN); }
	function defNbMaxQN ($v_iNbMaxQN) { $this->oEnregBdd->NbMaxQN = trim($v_iNbMaxQN); }
	function defMultiQN ($v_iMultiQN) { $this->oEnregBdd->NbMultiQN = trim($v_iMultiQN); } //Nombre réel
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQN () { return $this->oEnregBdd->EnonQN; }
	function retAlignEnonQN () { return $this->oEnregBdd->AlignEnonQN; }
	function retAlignRepQN () { return $this->oEnregBdd->AlignRepQN; }
	function retTxTAvQN () { return $this->oEnregBdd->TxtAvQN; }
	function retTxtApQN () { return $this->oEnregBdd->TxtApQN; }
	function retNbMinQN () { return $this->oEnregBdd->NbMinQN; }
	function retNbMaxQN () { return $this->oEnregBdd->NbMaxQN; }
	function retMultiQN () { return $this->oEnregBdd->MultiQN; } //Nombre réel
	
	/*
	** Fonction 		: cHtmlQNombre
	** Description	: renvoie le code html qui permet d'afficher une question de type nombre,
	**				     si $v_iIdFC est passé en paramètre la réponse correspondante sera également affichée
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/
	
	function cHtmlQNombre($v_iIdFC=NULL)
	{
		if ($v_iIdFC != NULL)
			$sValeur = retReponseFlottant($this->oBdd,$v_iIdFC,$this->iId);
		else
			$sValeur = "";
		
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQN = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQN);
		$this->oEnregBdd->TxtAvQN = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQN);
		$this->oEnregBdd->TxtApQN = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQN);
		
		//Genération du code html représentant l'objet
		//Ceci est le code COMPLET qui affiche toutes les valeurs -> pas utilisable 
		//tel quel par les etudiants
		$sCodeHtml = "\n<!--QNombre : {$this->oEnregBdd->IdObjFormul} -->\n"
					."<div align=\"{$this->oEnregBdd->AlignEnonQN}\">{$this->oEnregBdd->EnonQN}</div>"
					."<div class=\"InterER\" align=\"{$this->oEnregBdd->AlignRepQN}\">"
					."{$this->oEnregBdd->TxtAvQN} \n"
					."<input type=\"text\" name=\"{$this->oEnregBdd->IdObjFormul}\" size=\"10\" maxlength=\"10\" value=\"$sValeur\""
					." id=\"id_".$this->retId()."_".$this->retNbMinQN()."_".$this->retNbMaxQN()."\" onchange=\"validerQNombre(this);\" />"
					." {$this->oEnregBdd->TxtApQN}\n"
					."</div><br />\n";
		
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			// de les stocker dans la BD sans erreur 
			$EnonQN = validerTexte($this->oEnregBdd->EnonQN);
			$TxtAvQN = validerTexte($this->oEnregBdd->TxtAvQN);
			$TxtApQN = validerTexte($this->oEnregBdd->TxtApQN);
			
			//Valeur par défaut de MaxCar c'est la valeur de LargeurQTC
			if (strlen($this->oEnregBdd->MultiQN) < 1)
				$this->oEnregBdd->MultiQN = 1;
			
			$sRequeteSql = "REPLACE QNombre SET"
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", EnonQN='{$EnonQN}'"
						.", AlignEnonQN='{$this->oEnregBdd->AlignEnonQN}'"
						.", AlignRepQN='{$this->oEnregBdd->AlignRepQN}'"
						.", TxtAvQN='{$TxtAvQN}'"
						.", TxtApQN='{$TxtApQN}'"
						.", NbMinQN='{$this->oEnregBdd->NbMinQN}'"
						.", NbMaxQN='{$this->oEnregBdd->NbMaxQN}'"
						.", MultiQN='{$this->oEnregBdd->MultiQN}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function enregistrerRep($v_iIdFC,$v_iIdObjForm,$v_fReponsePersQTC)
	{
		if ($v_iIdObjForm != NULL)
		{
			$sRequeteSql = "REPLACE ReponseFlottant SET"
						." IdFC='{$v_iIdFC}'"
						.", IdObjFormul='{$v_iIdObjForm}'"
						.", Valeur='{$v_fReponsePersQTC}'";
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
		$EnonQN = validerTexte($this->oEnregBdd->EnonQN);
		$TxtAvQN = validerTexte($this->oEnregBdd->TxtAvQN);
		$TxtApQN = validerTexte($this->oEnregBdd->TxtApQN);
				
		$sRequeteSql = "INSERT INTO QNombre SET"									  
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", EnonQN='{$EnonQN}'"
					.", AlignEnonQN='{$this->oEnregBdd->AlignEnonQN}'"
					.", AlignRepQN='{$this->oEnregBdd->AlignRepQN}'"
					.", TxtAvQN='{$TxtAvQN}'"
					.", TxtApQN='{$TxtApQN}'"
					.", NbMinQN='{$this->oEnregBdd->NbMinQN}'"
					.", NbMaxQN='{$this->oEnregBdd->NbMaxQN}'" 		
					.", MultiQN='{$this->oEnregBdd->MultiQN}'"; 
			
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QNombre WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
