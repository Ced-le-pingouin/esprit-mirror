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
 * @file	fonctions_form.inc.php
 * 
 * Contient les fonctions communes aux formulaires
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */


	/*
	** Fonction 		: Alignement
	** Description		: Sert à cocher les cases concernant l'alignement d'un enonce
	**                    et de sa réponse
	** Entrée			: $sAlignEnon,$sAlignRep : contiennent la chaine de caractères 
	**										{left,right,center ou justify)
	** Sortie			: $ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4
	**					Une chaîne de caractère $aeX contient "CHECKED" et les autres ""	
	**					Une chaîne de caractère $arX contient "CHECKED" et les autres ""
	*/
	
function Alignement($sAlignEnon,$sAlignRep)
{
	$sAE1 = $sAE2 = $sAE3 = $sAE4 = "";
	$sAR1 = $sAR2 = $sAR3 = $sAR4 = "";	

	if ($sAlignEnon != "")
	{
		if ($sAlignEnon == "left") { $sAE1 = "checked=\"checked\""; }
		else if ($sAlignEnon == "right") { $sAE2 = "checked=\"checked\""; }
		else if ($sAlignEnon == "center") { $sAE3 = "checked=\"checked\""; }
		else if ($sAlignEnon == "justify") { $sAE4 = "checked=\"checked\""; }
	}
	else { $ae1 = "checked"; }
	
	if ($sAlignRep != "")
	{
		if ($sAlignRep == "left") { $sAR1 = "checked=\"checked\""; }
		else if ($sAlignRep == "right") { $sAR2 = "checked=\"checked\""; }
		else if ($sAlignRep == "center") { $sAR3 = "checked=\"checked\""; }
		else if ($sAlignRep == "justify") { $sAR4 = "checked=\"checked\""; }
	}
	else
	{ $ar1 = "checked=\"checked\""; }

	return array($sAE1,$sAE2,$sAE3,$sAE4,$sAR1,$sAR2,$sAR3,$sAR4);
}
	
function validerTexte($v_sTexte)
{
	return mysql_real_escape_string(stripslashes(trim($v_sTexte)));
}

	
	/*
	** Fonction 		: RetourPoidsReponse
	** Description		: renvoie pour chaque réponse appartenant a l'objet en cours de traitement
	**				  le poids pour chaque axe du formulaire même si celui-ci est NULL 
	** Entrée			: $v_iIdFormulaire,$v_iIdObjForm,$v_iIdReponse
	** Sortie			: Code Html contenant le(s) poids + mise en page + modification possible
	*/

function RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$v_iIdReponse)
{
	/*
	Utilisation de l'objet CBdd bcp plus léger pour faire les requêtes qu'un objet Projet
	Attention ne pas oublier le : require_once (dir_database("bdd.class.php"));
	*/
	$oCBdd2 = new CBdd;
	/*
	Cette requête retourne pour chaque réponse, n lignes représentant chaque axe du formulaire.
	Chaque ligne contient la valeur de l'axe[poids] si elle existe sinon contient la valeur NULL pour cet axe. 
	Exemple de partie de résultat :
	+-------+---------------+-----------+--------------+--------------+-------------+--------+
	| IdAxe | DescAxe       | IdReponse | TexteReponse | OrdreReponse | IdObjFormul | Poids  |
	+-------+---------------+-----------+--------------+--------------+-------------+--------+
	|     1 | Determination |        55 |              |            3 |         123 | [NULL] |
	|     2 | Objectivite   |        55 |              |            3 |         123 | [NULL] |
	+-------+---------------+-----------+--------------+--------------+-------------+--------+
	*/

	$sRequeteSqlAxes =	 "Select"
							 ." a.*"
							 .", pr.*"
							 .", ra.Poids"
						 ." FROM"
							 ." Formulaire_Axe as fa"
							 .", Axe as a"
							 .", PropositionReponse as pr"
							 ." LEFT JOIN Reponse_Axe as ra ON (pr.IdPropRep = ra.IdPropRep AND a.IdAxe = ra.IdAxe)"
					  	 ." WHERE"
							 ." fa.IdForm = '{$v_iIdFormulaire}' AND fa.IdAxe = a.IdAxe"
							 ." AND pr.IdObjFormul = '{$v_iIdObjForm}'"
							 ." AND pr.IdPropRep = '{$v_iIdReponse}'"
						 ." ORDER BY"
							 ." pr.OrdrePropRep"
							 .", a.DescAxe";
	
	$hResultAxe = $oCBdd2->executerRequete($sRequeteSqlAxes);

	$sCodeHtml="";

	while ($oEnreg = $oCBdd2->retEnregSuiv($hResultAxe))
	{
		//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
		$iPoids= $oEnreg->Poids;
		$iIdAxe = $oEnreg->IdAxe;
		$iIdReponse = $oEnreg->IdReponse;
		$sDescAxe = $oEnreg->DescAxe;
	
		$sCodeHtml.="<tr>\n<td>\n &nbsp;\n</td>\n<td>\n"
				  ."<table>\n<tr>\n<td width=\"200\">\n &#8226; $sDescAxe\n</td>\n<td>\n <input type=\"text\" size=\"4\" maxlength=\"4\" "
				  ."name=\"repAxe[$iIdReponse][$iIdAxe]\" value=\"$iPoids\" onblur=\"verifNumeric(this)\" />\n</td>\n</tr>\n</table>\n"
				  ."</td>\n</tr>\n"; 
	}

	return $sCodeHtml;
}

function CopierUnFormulaire(&$v_oBdd,$v_iIdFormulaire,$iIdNvPers)
{
	$this->oBdd = &$v_oBdd;
	
	//Copie de formulaire
	$oFormulaire = new CFormulaire($this->oBdd,$v_iIdFormulaire);
	$v_iIdNvFormulaire = $oFormulaire->copier($iIdNvPers);  //On envoie l'IdPers du futur propriétaire de la copie
	
	//Copie des axes du formulaires
	CopieFormulaire_Axe($this->oBdd,$v_iIdFormulaire,$v_iIdNvFormulaire);
	
	//Copie des objets du formulaire 1 par 1
	$hResult = $this->oBdd->executerRequete("SELECT * FROM ObjetFormulaire WHERE IdFormul=$v_iIdFormulaire");
  
	while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		CopierUnObjetFormulaire($this->oBdd, $oEnreg, $v_iIdNvFormulaire);

	$this->oBdd->libererResult($hResult);
	return $v_iIdNvFormulaire;
}

function CopierUnObjetFormulaire(&$v_oBdd, &$v_oObjForm, $v_iIdFormulaireDest, $v_iOrdreObjet = NULL)
{
	if (!is_object($v_oObjForm) && is_numeric($v_oObjForm))
	{
		$oObjetFormulaire = new CObjetFormulaire($v_oBdd, $v_oObjForm);
	}
	else
	{
		$oObjetFormulaire = new CObjetFormulaire($v_oBdd);
		$oObjetFormulaire->init($v_oObjForm);
	}
	
	$iIdObjActuel = $oObjetFormulaire->retId();
	//echo"<br>iIdObjActuel : ".$iIdObjActuel;
	$iIdNvObjForm = $oObjetFormulaire->copier($v_iIdFormulaireDest, $iIdObjActuel, $v_iOrdreObjet);
	//echo"<br>iIdNvObjForm : ".$iIdNvObjForm;
	
	switch($oObjetFormulaire->retIdTypeObj())
	{
		case 1:
			//echo "Objet de type 1<br>";
			$oQTexteLong = new CQTexteLong($v_oBdd,$iIdObjActuel);
			$oQTexteLong->copier($iIdNvObjForm);
			break;
		
		case 2:
			//echo "Objet de type 2<br>";
			$oQTexteCourt = new CQTexteCourt($v_oBdd,$iIdObjActuel);
			$oQTexteCourt->copier($iIdNvObjForm);
			break;
		
		case 3:
			//echo "Objet de type 3<br>";
			$oQNombre = new CQNombre($v_oBdd,$iIdObjActuel);
			$oQNombre->copier($iIdNvObjForm);
			break;
		
		case 4:
			//echo "Objet de type 4<br>";
			$oQListeDeroul = new CQListeDeroul($v_oBdd,$iIdObjActuel);
			$oQListeDeroul->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 5:
			//echo "Objet de type 5<br>";
			$oQRadio = new CQRadio($v_oBdd,$iIdObjActuel);
			$oQRadio->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 6:
			//echo "Objet de type 6<br>";
			$oQCocher = new CQCocher($v_oBdd,$iIdObjActuel);
			$oQCocher->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 7:
			//echo "Objet de type 7<br>";
			$oMPTexte = new CMPTexte($v_oBdd,$iIdObjActuel);
			$oMPTexte->copier($iIdNvObjForm);
			break;
		
		case 8:
			//echo "Objet de type 8<br>";
			$oMPSeparateur = new CMPSeparateur($v_oBdd,$iIdObjActuel);
			$oMPSeparateur->copier($iIdNvObjForm);
			break;
		
		default:
			echo "Erreur IdObjForm invalide<br>";
	} //Fin switch
	
	return $iIdNvObjForm;
}

function CopieReponses(&$v_oBdd,$v_iIdObjFormul,$v_iIdNvObjForm)
{
	$this->oBdd = &$v_oBdd;
	$hResult2 = $this->oBdd->executerRequete("SELECT * FROM PropositionReponse WHERE IdObjFormul = $v_iIdObjFormul");
										  
	while ($oEnreg2 = $this->oBdd->retEnregSuiv($hResult2))
	{
		$oPropositionReponse = new CPropositionReponse($this->oBdd);
		$oPropositionReponse->init($oEnreg2);
		$iIdReponse = $oPropositionReponse->retId();
		$iIdNvReponse = $oPropositionReponse->copier($v_iIdNvObjForm);
		CopieReponse_Axe($this->oBdd,$iIdReponse,$iIdNvReponse);
	}
	$this->oBdd->libererResult($hResult2);
}

function CopieReponse_Axe(&$v_oBdd,$v_iIdReponse,$v_iIdNvReponse)
{
	$this->oBdd = &$v_oBdd;
	
	$sRequeteSql = "SELECT * FROM Reponse_Axe"
						." WHERE IdPropRep = $v_iIdReponse";
	$hResult3 = $this->oBdd->executerRequete($sRequeteSql);
											  
	while ($oEnreg3 = $this->oBdd->retEnregSuiv($hResult3))
	{
		$oReponse_Axe = new CReponse_Axe($this->oBdd);
		$oReponse_Axe->init($oEnreg3);
		$oReponse_Axe->copier($v_iIdNvReponse);
	}
	$this->oBdd->libererResult($hResult3);
}


function CopieFormulaire_Axe(&$v_oBdd,$v_iIdForm,$v_iIdNvForm)
{
	$this->oBdd = &$v_oBdd;
	$hResult4 = $this->oBdd->executerRequete("SELECT * FROM Formulaire_Axe"
											  ." WHERE IdForm = $v_iIdForm");
										  
	while ($oEnreg4 = $this->oBdd->retEnregSuiv($hResult4))
	{
		$oFormulaire_Axe = new CFormulaire_Axe($this->oBdd);
		$oFormulaire_Axe->init($oEnreg4);
		$oFormulaire_Axe->copier($v_iIdNvForm);
	}
	$this->oBdd->libererResult($hResult4);
	return TRUE;
}
?>
