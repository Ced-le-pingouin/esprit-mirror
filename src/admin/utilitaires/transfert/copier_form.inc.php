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
** Fichier .................: copier_form.inc.php
** Description ............:
** Date de création .......: 26/08/2004
** Dernière modification ..: 27/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

function afficher_col_copier ($v_iNiveau,$v_sType,$v_sNom)
{
	echo "<tr>"
		."<td style=\"padding-left: ".($v_iNiveau*10)."px;\">"
		."<b>{$v_sType}</b>"
		."<br>"
		."<small>".stripslashes($v_sNom)."</small>"
		."</td>";
}

function afficher_col_statut ()
{
	echo "<td style=\"color: rgb(100,255,100); font-weight: bold; text-align: center;\">Ok</td></tr>\n";
}

function rechercher_forum ($v_iIdTypeForum,$v_iTypeForum)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	$oEnreg = NULL;
	
	switch ($v_iTypeForum)
	{
		case TYPE_MODULE:
			$sNomChampTypeForum = "IdMod";
			break;
		case TYPE_RUBRIQUE:
			$sNomChampTypeForum = "IdRubrique";
			break;
		case TYPE_SOUS_ACTIVITE:
			$sNomChampTypeForum = "IdSousActiv";
			break;
		default:
			return NULL;
	}
	
	$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Forum"
		." WHERE {$sNomChampTypeForum}='{$v_iIdTypeForum}'"
		." LIMIT 1";
	$hResult = mysql_query($sRequeteSql,$hConnexion);
	
	if ($hResult !== FALSE)
	{
		$oEnreg = mysql_fetch_object($hResult);
		mysql_free_result($hResult);
	}
	
	return $oEnreg;
}

function copier_forum (&$v_oForumSrc,$v_iIdNouveauLien,$v_iTypeForum)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	if (!isset($v_oForumSrc) &&
		!isset($v_oForumSrc->NomForum))
		return 0;
	
	$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Forum SET"
		." IdForum=NULL"
		.", NomForum='".MySQLEscapeString($v_oForumSrc->NomForum)."'"
		.", DateForum=NOW()"
		.", ModaliteForum='3'"
		.", StatutForum='".(isset($v_oForumSrc->StatutForum) ? $v_oForumSrc->StatutForum : 0)."'"
		.", AccessibleVisiteursForum='1'"
		.", OrdreForum='".(isset($v_oForumSrc->OrdreForum) ? $v_oForumSrc->OrdreForum : 1)."'"
		.", IdForumParent='".(isset($v_oForumSrc->IdForumParent) ? $v_oForumSrc->IdForumParent : 0)."'"
		.", IdMod='".($v_iTypeForum == TYPE_MODULE ? $v_iIdNouveauLien : 0)."'"
		.", IdRubrique='".($v_iTypeForum == TYPE_RUBRIQUE ? $v_iIdNouveauLien : 0)."'"
		.", IdSousActiv='".($v_iTypeForum == TYPE_SOUS_ACTIVITE ? $v_iIdNouveauLien : 0)."'"
		.", IdPers='".(isset($v_oForumSrc->IdPers) ? $v_oForumSrc->IdPers : 0)."'";
	mysql_query($sRequeteSql,$hConnexion);
	
	return mysql_insert_id($hConnexion);
}

function rechercher_sujets_forum ($v_iIdForum)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	$aoSujets = array();
	
	$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.SujetForum"
		." WHERE IdForum='{$v_iIdForum}'";
	$hResult = mysql_query($sRequeteSql,$hConnexion);
	
	if ($hResult !== FALSE)
	{
		while ($oEnreg = mysql_fetch_object($hResult))
			$aoSujets[] = $oEnreg;
		
		mysql_free_result($hResult);
	}
	
	return $aoSujets;
}

function copier_sujets_forum (&$v_oForumSrc,$v_iIdForumNouv)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	$aoSujets = rechercher_sujets_forum($v_oForumSrc->IdForum);
	
	foreach ($aoSujets as $oSujet)
	{
		if (!isset($oSujet) ||
			!isset($oSujet->TitreSujetForum))
			continue;
		
		$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.SujetForum SET"
			." IdSujetForum=NULL"
			.", TitreSujetForum='".MySQLEscapeString($oSujet->TitreSujetForum)."'"
			.", DateSujetForum=NOW()"
			.", ModaliteSujetForum='{$oSujet->ModaliteSujetForum}'"
			.", StatutSujetForum='{$oSujet->StatutSujetForum}'"
			.", AccessibleVisiteursSujetForum='{$oSujet->AccessibleVisiteursSujetForum}'"
			.", IdForum='{$v_iIdForumNouv}'"
			.", IdPers='{$oSujet->IdPers}'";
		mysql_query($sRequeteSql,$hConnexion);
	}
}

/**
 * Galerie
 */
function verifier_associer_galerie ($v_iIdSousActivCollectAncien,$v_iIdSousActivCollectNouv,&$v_aaGaleries)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.SousActiv_SousActiv"
		." WHERE IdSousActivRef='{$v_iIdSousActivCollectAncien}'";
	$hResult = mysql_query($sRequeteSql,$hConnexion);
	
	if ($hResult !== FALSE)
	{
		while ($oEnreg = mysql_fetch_object($hResult))
			$v_aaGaleries[] = array($oEnreg->IdSousActiv,$oEnreg->IdSousActivRef,0,$v_iIdSousActivCollectNouv);
		
		mysql_free_result($hResult);
	}
}

function associer_galerie_collecticiels ($v_iIdSousActivGalerieAncien,$v_iIdSousActivGalerieNouv,&$v_aaGaleries)
{
	for ($i=0; $i<count($v_aaGaleries); $i++)
		if ($v_aaGaleries[$i][0] == $v_iIdSousActivGalerieAncien)
			$v_aaGaleries[$i][2] = $v_iIdSousActivGalerieNouv;
}

function ajouter_collecticiels_galeries ($v_aaGaleries)
{
	global $hConnexion;
	global $url_sNomBddDst;
	
	$sValeursRequete = NULL;
	
	foreach ($v_aaGaleries as $aGalerie)
		if ($aGalerie[2] > 0 && $aGalerie[3] > 0)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$aGalerie[2]}','{$aGalerie[3]}')";
	
	if (isset($sValeursRequete))
	{
		$sRequeteSql = "REPLACE INTO {$url_sNomBddDst}.SousActiv_SousActiv"
			." (IdSousActiv, IdSousActivRef)"
			." VALUES"
			." {$sValeursRequete}";
		mysql_query($sRequeteSql,$hConnexion);
	}
}

function rechercher_chats ($v_iIdTypeChat,$v_iTypeChat)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	$aoChats = array();
	
	$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Chat"
		." WHERE IdSousActiv='{$v_iIdTypeChat}'";
	$hResult = mysql_query($sRequeteSql,$hConnexion);
	
	if ($hResult !== FALSE)
	{
		while ($oEnreg = mysql_fetch_object($hResult))
			$aoChats[] = $oEnreg;
		
		mysql_free_result($hResult);
	}
	
	return $aoChats;
}

function copier_chats (&$v_aoChats,$v_iIdTypeChat)
{
	global $hConnexion;
	global $url_sNomBddSrc, $url_sNomBddDst;
	
	foreach ($v_aoChats as $oChat)
	{
		// Ne copier que les chats public
		if (!isset($oChat->NomChat) ||
			$oChat->ModaliteChat == 1)
			continue;
		
		$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Chat SET"
			." IdChat=NULL"
			.", NomChat='".MySQLEscapeString($oChat->NomChat)."'"
			.", CouleurChat='{$oChat->CouleurChat}'"
			.", ModaliteChat='{$oChat->ModaliteChat}'"
			.", EnregChat='{$oChat->EnregChat}'"
			.", OrdreChat='{$oChat->OrdreChat}'"
			.", SalonPriveChat ='{$oChat->SalonPriveChat}'"
			.", IdSousActiv='{$v_iIdTypeChat}'";
		mysql_query($sRequeteSql,$hConnexion);
	}
}

function ajouter_intitule ()
{
}

?>
