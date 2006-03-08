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

/*
** Fichier ................: qnombre.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CQNombre 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;

	function CQNombre(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
							  //si 0 crée un objet presque vide sinon 
							  //rempli l'objet avec les données de la table QNombre
							  //de l'elément ayant l'Id passé en argument 
							  //(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
	function init ($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT *"
				." FROM QNombre"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type nombre, avec tous ses champs vides, en fin de table
	{
		$sRequeteSql = "INSERT INTO QNombre SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return ($this->iId = $this->oBdd->retDernierId());
	}


	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQN ($v_sEnonQN) { $this->oEnregBdd->EnonQN = $v_sEnonQN; }
	function defAlignEnonQN ($v_sAlignEnonQN) { $this->oEnregBdd->AlignEnonQN = $v_sAlignEnonQN; }
	function defAlignRepQN ($v_sAlignRepQN) { $this->oEnregBdd->AlignRepQN = $v_sAlignRepQN; }
	function defTxtAvQN ($v_sTxtAvQN) { $this->oEnregBdd->TxtAvQN = $v_sTxtAvQN; }
	function defTxtApQN ($v_sTxtApQN) { $this->oEnregBdd->TxtApQN = $v_sTxtApQN; }
	function defNbMinQN ($v_iNbMinQN) { $this->oEnregBdd->NbMinQN = trim($v_iNbMinQN); }
	function defNbMaxQN ($v_iNbMaxQN) { $this->oEnregBdd->NbMaxQN = trim($v_iNbMaxQN); }
	function defMultiQN ($v_iMultiQN) { $this->oEnregBdd->NbMultiQN = trim($v_iMultiQN); } //Nombre réel
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
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
		$sValeur = "";
	
		if ($v_iIdFC != NULL)
		{
			$sRequeteSql =
				"SELECT * FROM ReponseFlottant"
				." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$sValeur = $oEnregRep->Valeur;
		}
	
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQN = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQN);
		$this->oEnregBdd->TxtAvQN = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQN);
		$this->oEnregBdd->TxtApQN = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQN);
	
		//Genération du code html représentant l'objet
		//Ceci est le code COMPLET qui affiche toutes les valeurs -> pas utilisable 
		//tel quel par les etudiants
		$sCodeHtml="\n<!--QNombre : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQN}>{$this->oEnregBdd->EnonQN}</div>"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQN}>"
			."{$this->oEnregBdd->TxtAvQN} \n"
			."<input type=\"text\" name=\"{$this->oEnregBdd->IdObjForm}\" SIZE=\"10\" MAXLENGTH=\"10\" VALUE=\"$sValeur\""
				." id=\"".$this->retId()."_".$this->retNbMinQN()."_".$this->retNbMaxQN()."\" onChange=\"validerQNombre(this);\">"
			." {$this->oEnregBdd->TxtApQN}\n"
			."</div><br>\n";
		
		return $sCodeHtml;
	}

	function cHtmlQTexteLong($v_iIdFC=NULL)
	{
	
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQTL = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQTL);
		
		//Genération du code html représentant l'objet
		$sCodeHtml="\n<!--QTexteLong : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQTL}>{$this->oEnregBdd->EnonQTL}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQTL}>\n"
			."<TEXTAREA NAME=\"{$this->oEnregBdd->IdObjForm}\" ROWS=\"{$this->oEnregBdd->HauteurQTL}\" COLS=\"{$this->oEnregBdd->LargeurQTL}\">\n"
			."$sValeur"
			."</TEXTAREA>\n"
			."</div><br>\n";
		   
		return $sCodeHtml;
	}

	function cHtmlQNombreModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
		
		$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = $sMessageErreur4 = "";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQN = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQN = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQN = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->TxtAvQN = stripslashes($HTTP_POST_VARS['TxtAv']);
			$this->oEnregBdd->TxtApQN = stripslashes($HTTP_POST_VARS['TxtAp']);
			$this->oEnregBdd->NbMinQN = $HTTP_POST_VARS['NbMin'];
			$this->oEnregBdd->NbMaxQN = $HTTP_POST_VARS['NbMax'];
			$this->oEnregBdd->MultiQN = $HTTP_POST_VARS['Multi'];
				
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			//if (strlen($HTTP_POST_VARS['Enonce']) < 1) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!is_numeric($HTTP_POST_VARS['NbMin'])) { $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!is_numeric($HTTP_POST_VARS['NbMax'])) {$sMessageErreur3="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!is_numeric($HTTP_POST_VARS['Multi'])) { $sMessageErreur4="<font color =\"red\">*</font>"; $iFlagErreur=1;}
				
			if ($iFlagErreur == 0) 
			{
				$this->enregistrer();
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
			} //si pas d'erreur, enregistrement physique
		}
		
		// La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
		// et les 6 autres contiennent une chaîne vide
		// aeX = alignement enoncé, arX = alignement réponse
		list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignEnonQN,$this->oEnregBdd->AlignRepQN);
		
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
		
		$sCodeHtml = 
			"<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">"
			."<fieldset><legend><b>ENONCE</b></legend>"
			."<TABLE>"
			."<TR>"
			."<TD>$sMessageErreur1 Enoncé :</TD>"
			."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\">{$this->oEnregBdd->EnonQN}</textarea></TD>"
			."</TR><TR>"
			."<TD>Alignement énoncé :</TD>"
			."<TD><INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"left\" $ae1>Gauche"
			."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"right\" $ae2>Droite"
			."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"center\" $ae3>Centrer"
			."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"justify\" $ae4>Justifier"
			."</TD>"
			."</TR>"
			."</TABLE>"
			."</fieldset>"
		   
			."<fieldset><legend><b>REPONSE</b></legend>"
			."<TABLE>"
			."<TR>"
			."<TD>Texte avant la réponse :</TD>"
			."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAv\" Value=\"{$this->oEnregBdd->TxtAvQN}\"></TR>"
			."</TR><TR>"
			."<TD>Texte après la réponse :</TD>"
			."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAp\" Value=\"{$this->oEnregBdd->TxtApQN}\"></TR>"
			."</TR><TR>"
			."<TD>$sMessageErreur2 Nombre minimum :</TD>"
			."<TD><input type=\"text\" size=\"10\" maxlength=\"9\" name=\"NbMin\" Value=\"{$this->oEnregBdd->NbMinQN}\" onblur=\"verifNumeric(this)\"></TD>"
			."</TR><TR>"
			."<TD>$sMessageErreur3 Nombre maximum :</TD>"
			."<TD><input type=\"text\" size=\"10\" maxlength=\"10\" name=\"NbMax\" Value=\"{$this->oEnregBdd->NbMaxQN}\" onblur=\"verifNumeric(this)\"></TD>"
			."</TR><TR>"
			."<TD>$sMessageErreur4 Coefficient multiplicateur :</TD>"
			."<TD><input type=\"text\" size=\"5\" maxlength=\"10\" name=\"Multi\" Value=\"{$this->oEnregBdd->MultiQN}\" onblur=\"verifNumeric(this)\"></TD>"
			."</TR><TR>"
			."<TD>Alignement Réponse :</TD>"
			."<TD><INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"left\" $ar1>Gauche"
			."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"right\" $ar2>Droite"
			."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"center\" $ar3>Centrer"
			."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"justify\" $ar4>Justifier"
			."</TD>"
			."</TR>"
			."</TABLE>"
			."</fieldset>"
			
			// Le champ caché ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
			."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
			."</form>";
			
		return $sCodeHtml;
	}

	function enregistrer ()
	{
		if ($this->oEnregBdd->IdObjForm !=NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			// de les stocker dans la BD sans erreur 
			$EnonQN = validerTexte($this->oEnregBdd->EnonQN);
			$TxtAvQN = validerTexte($this->oEnregBdd->TxtAvQN);
			$TxtApQN = validerTexte($this->oEnregBdd->TxtApQN);
			
			//Valeur par défaut de MaxCar c'est la valeur de LargeurQTC
			if (strlen($this->oEnregBdd->MultiQN) < 1)
				$this->oEnregBdd->MultiQN = 1;
				
			$sRequeteSql =
				"REPLACE QNombre SET"
				." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
				.", EnonQN='{$EnonQN}'"
				.", AlignEnonQN='{$this->oEnregBdd->AlignEnonQN}'"
				.", AlignRepQN='{$this->oEnregBdd->AlignRepQN}'"
				.", TxtAvQN='{$TxtAvQN}'"
				.", TxtApQN='{$TxtApQN}'"
				.", NbMinQN='{$this->oEnregBdd->NbMinQN}'"
				.", NbMaxQN='{$this->oEnregBdd->NbMaxQN}'"
				.", MultiQN='{$this->oEnregBdd->MultiQN}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}

	function enregistrerRep ($v_iIdFC,$v_iIdObjForm,$v_fReponsePersQTC)
	{
		if ($v_iIdObjForm !=NULL)
		{
			$sRequeteSql = "REPLACE ReponseFlottant SET"
				." IdFC='{$v_iIdFC}'"
				.", IdObjForm='{$v_iIdObjForm}'"
				.", Valeur='{$v_fReponsePersQTC}'";
				
			//echo "<br>enregistrer ReponsePersQTL : ".$sRequeteSql;
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
		$EnonQN = validerTexte($this->oEnregBdd->EnonQN);
		$TxtAvQN = validerTexte($this->oEnregBdd->TxtAvQN);
		$TxtApQN = validerTexte($this->oEnregBdd->TxtApQN);
				
		$sRequeteSql = "INSERT INTO QNombre SET"									  
			." IdObjForm='{$v_iIdNvObjForm}'"
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


	function effacer ()
	{
		$sRequeteSql = "DELETE FROM QNombre"
				." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer QNombre()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
}

?>
