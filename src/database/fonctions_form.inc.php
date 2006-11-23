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

/**
 * Sert à cocher les cases concernant l'alignement d'un énonce et de sa réponse
 * 
 * @param	sAlignEnon	contient la chaine de caractère {left,right,center ou justify) pour l'alignement de l'énoncé
 * @param	sAlignRep	contient la chaine de caractère {left,right,center ou justify) pour l'alignement de la réponse
 * 
 * @return	un tableau contenant des chaînes de caractères, deux contenant 'checked="checked"' et les autres sont vides
 */
function Alignement($sAlignEnon,$sAlignRep)
{
	$sAE1 = $sAE2 = $sAE3 = $sAE4 = "";
	$sAR1 = $sAR2 = $sAR3 = $sAR4 = "";	
	
	switch($sAlignEnon)
	{
		case "right":	$sAE2 = "checked=\"checked\"";
						break;
		case "center":	$sAE3 = "checked=\"checked\"";
						break;
		case "justify":	$sAE4 = "checked=\"checked\"";
						break;
		default:		$sAE1 = "checked=\"checked\"";
	}
	
	switch($sAlignRep)
	{
		case "right":	$sAR2 = "checked=\"checked\"";
						break;
		case "center":	$sAR3 = "checked=\"checked\"";
						break;
		case "justify":	$sAR4 = "checked=\"checked\"";
						break;
		default:		$sAR1 = "checked=\"checked\"";
	}
	
	return array($sAE1,$sAE2,$sAE3,$sAE4,$sAR1,$sAR2,$sAR3,$sAR4);
}

/**
 * Supprime les espaces (ou d'autres caractères) en début et fin de chaîne, supprime les anti-slash d'une chaîne, et protège les caractères spéciaux d'une commande SQL
 * 
 * @param	v_sTexte la chaîne de caractère à traiter
 * 
 * @return	 la chaîne de caractère traitée
 */
function validerTexte($v_sTexte)
{
	return mysql_real_escape_string(stripslashes(trim($v_sTexte)));
}

/**
 * Retourne les id des propositions de réponses que l'étudiant a choisies
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdFC			l'id du formulaire (activité en ligne) complété
 * @param	v_iIdObjFormul	l'id de l'objet formulaire
 * 
 * @return	un tableau contenant les id des propositions de réponses que l'étudiant a choisies
 */
function retReponseEntier(&$v_oBdd,$v_iIdFC,$v_iIdObjFormul)
{
	$aiReponse = array();
	$sRequeteSql = "SELECT IdPropRep FROM ReponseEntier WHERE IdFC='{$v_iIdFC}' AND IdObjFormul='{$v_iIdObjFormul}'";
	$hResultRep = $v_oBdd->executerRequete($sRequeteSql);
	while($oEnregRep = $v_oBdd->retEnregSuiv($hResultRep))
		$aiReponse[] = $oEnregRep->IdPropRep;
	$v_oBdd->libererResult($hResultRep);
	return $aiReponse;
}

/**
 * Renvoie pour chaque proposition de réponse de l'objet de formulaire courant le poids  pour chaque axe de l'activité en ligne
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdFormulaire	l'id de l'activité en ligne (formulaire)
 * @param	v_iIdObjFormul	l'id de l'objet formulaire
 * @param	v_iIdPropRep	l'id de la proposition de réponse
 * 
 * @return	Code Html contenant le(s) poid(s) + mise en page + modification possible
 */
function RetourPoidsReponse(&$v_oBdd,$v_iIdFormulaire,$v_iIdObjFormul,$v_iIdPropRep)
{
/*	Cette requête retourne pour chaque réponse, n lignes représentant chaque axe du formulaire.
	Chaque ligne contient la valeur de l'axe[poids] si elle existe sinon contient la valeur NULL pour cet axe. 
	Exemple de partie de résultat :
	+-------+---------------+-----------+--------------+--------------+-------------+--------+
	| IdAxe | DescAxe       | IdPropRep | TexteReponse | OrdreReponse | IdObjFormul | Poids  |
	+-------+---------------+-----------+--------------+--------------+-------------+--------+
	|     1 | Determination |        55 |              |            3 |         123 | [NULL] |
	|     2 | Objectivite   |        55 |              |            3 |         123 | [NULL] |
	+-------+---------------+-----------+--------------+--------------+-------------+--------+	*/
	$sRequeteSqlAxes =	 "Select a.*, pr.*, ra.Poids"
						 ." FROM"
							 ." Formulaire_Axe as fa"
							 .", Axe as a"
							 .", PropositionReponse as pr"
							 ." LEFT JOIN Reponse_Axe as ra ON (pr.IdPropRep = ra.IdPropRep AND a.IdAxe = ra.IdAxe)"
					  	 ." WHERE"
							 ." fa.IdFormul = '{$v_iIdFormulaire}' AND fa.IdAxe = a.IdAxe"
							 ." AND pr.IdObjFormul = '{$v_iIdObjFormul}'"
							 ." AND pr.IdPropRep = '{$v_iIdPropRep}'"
						 ." ORDER BY pr.OrdrePropRep, a.DescAxe";
	$hResultAxe = $v_oBdd->executerRequete($sRequeteSqlAxes);
	$sCodeHtml= "";
	while ($oEnreg = $v_oBdd->retEnregSuiv($hResultAxe))
	{
		//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
		$iPoids= $oEnreg->Poids;
		$iIdAxe = $oEnreg->IdAxe;
		$iIdPropRep = $oEnreg->IdPropRep;
		$sDescAxe = $oEnreg->DescAxe;
		$sCodeHtml.="<div class=\"poidsaxe\">\n"
				  ."&#8226; $sDescAxe <input type=\"text\" size=\"4\" maxlength=\"4\" name=\"repAxe[$iIdPropRep][$iIdAxe]\" value=\"$iPoids\" onblur=\"verifNumeric(this)\" />\n"
				  ."</div>\n"; 
	}
	$v_oBdd->libererResult($hResultAxe);
	return $sCodeHtml;
}

/**
 * Copie une activité en ligne
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdFormulaire	l'id de l'activité en ligne (formulaire)
 * @param	iIdNvPers		l'id de la personne
 * 
 * @return	l'id de l'activité en ligne (formulaire) copiée
 */
function CopierUnFormulaire(&$v_oBdd,$v_iIdFormulaire,$iIdNvPers)
{
	//Copie de formulaire
	$oFormulaire = new CFormulaire($v_oBdd,$v_iIdFormulaire);
	$v_iIdNvFormulaire = $oFormulaire->copier($iIdNvPers);  //On envoie l'IdPers du futur propriétaire de la copie
	
	//Copie des axes du formulaires
	CopieFormulaire_Axe($v_oBdd,$v_iIdFormulaire,$v_iIdNvFormulaire);
	
	//Copie des objets du formulaire 1 par 1
	$hResult = $v_oBdd->executerRequete("SELECT * FROM ObjetFormulaire WHERE IdFormul=$v_iIdFormulaire");
	while($oEnreg = $v_oBdd->retEnregSuiv($hResult))
		CopierUnObjetFormulaire($v_oBdd, $oEnreg, $v_iIdNvFormulaire);
	$v_oBdd->libererResult($hResult);
	
	return $v_iIdNvFormulaire;
}
/**
 * Copie un objet de formulaire
 * 
 * @param	v_oBdd					Objet représentant la connexion à la DB
 * @param	v_oObjForm				Objet contenant un enregistrement de la table ObjetFormulaire
 * @param	v_iIdFormulaireDest		l'id de l'objet de formulaire de destination
 * @param	v_iOrdreObjet			le numéro d'ordre du nouvel objet de formulaire
 * 
 * @return	l'id du nouvel objet de formulaire
 */
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
	$iIdNvObjForm = $oObjetFormulaire->copier($v_iIdFormulaireDest, $iIdObjActuel, $v_iOrdreObjet);
	
	switch($oObjetFormulaire->retIdTypeObj())
	{
		case 1:	$oQTexteLong = new CQTexteLong($v_oBdd,$iIdObjActuel);
				$oQTexteLong->copier($iIdNvObjForm);
				break;
		
		case 2:	$oQTexteCourt = new CQTexteCourt($v_oBdd,$iIdObjActuel);
				$oQTexteCourt->copier($iIdNvObjForm);
				break;
		
		case 3:	$oQNombre = new CQNombre($v_oBdd,$iIdObjActuel);
				$oQNombre->copier($iIdNvObjForm);
				break;
		
		case 4:	$oQListeDeroul = new CQListeDeroul($v_oBdd,$iIdObjActuel);
				$oQListeDeroul->copier($iIdNvObjForm);
				CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
		
		case 5:	$oQRadio = new CQRadio($v_oBdd,$iIdObjActuel);
				$oQRadio->copier($iIdNvObjForm);
				CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
		
		case 6:	$oQCocher = new CQCocher($v_oBdd,$iIdObjActuel);
				$oQCocher->copier($iIdNvObjForm);
				CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
		
		case 7:	$oMPTexte = new CMPTexte($v_oBdd,$iIdObjActuel);
				$oMPTexte->copier($iIdNvObjForm);
				break;
		
		case 8:	$oMPSeparateur = new CMPSeparateur($v_oBdd,$iIdObjActuel);
				$oMPSeparateur->copier($iIdNvObjForm);
				break;
	}
	return $iIdNvObjForm;
}

/**
 * Copie les propositions de réponses d'un objet de formulaire vers un autre
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdObjFormul	l'id de l'objet formulaire à copier
 * @param	v_iIdNvObjForm	l'id de l'objet formulaire de destination
 */
function CopieReponses(&$v_oBdd,$v_iIdObjFormul,$v_iIdNvObjForm)
{
	$hResult = $v_oBdd->executerRequete("SELECT * FROM PropositionReponse WHERE IdObjFormul = $v_iIdObjFormul");
										  
	while($oEnreg = $v_oBdd->retEnregSuiv($hResult))
	{
		$oPropositionReponse = new CPropositionReponse($v_oBdd);
		$oPropositionReponse->init($oEnreg);
		$iIdReponse = $oPropositionReponse->retId();
		$iIdNvReponse = $oPropositionReponse->copier($v_iIdNvObjForm);
		CopieReponse_Axe($v_oBdd,$iIdReponse,$iIdNvReponse);
	}
	$v_oBdd->libererResult($hResult);
}

/**
 * Copie les poids des propositions de réponses par rapport à un axe
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdReponse	l'id de la proposition de réponse
 * @param	v_iIdNvReponse	l'id de la nouvelle proposition de réponse
 */
function CopieReponse_Axe(&$v_oBdd,$v_iIdPropRep,$v_iIdNvReponse)
{
	$sRequeteSql = "SELECT * FROM Reponse_Axe WHERE IdPropRep = $v_iIdPropRep";
	$hResult = $v_oBdd->executerRequete($sRequeteSql);
											  
	while($oEnreg = $v_oBdd->retEnregSuiv($hResult))
	{
		$oReponse_Axe = new CReponse_Axe($v_oBdd);
		$oReponse_Axe->init($oEnreg);
		$oReponse_Axe->copier($v_iIdNvReponse);
	}
	$v_oBdd->libererResult($hResult);
}

/**
 * Copie le lien (Axe - Formulaire) entre deux activités en lignes
 * 
 * @param	v_oBdd			Objet représentant la connexion à la DB
 * @param	v_iIdForm		l'id de l'activité en ligne (formulaire) source
 * @param	v_iIdNvForm		l'id de l'activité en ligne de destination
 */
function CopieFormulaire_Axe(&$v_oBdd,$v_iIdForm,$v_iIdNvForm)
{
	$hResult = $v_oBdd->executerRequete("SELECT * FROM Formulaire_Axe WHERE IdFormul = $v_iIdForm");
										  
	while($oEnreg = $v_oBdd->retEnregSuiv($hResult))
	{
		$oFormulaire_Axe = new CFormulaire_Axe($v_oBdd);
		$oFormulaire_Axe->init($oEnreg);
		$oFormulaire_Axe->copier($v_iIdNvForm);
	}
	$v_oBdd->libererResult($hResult);
}
?>
