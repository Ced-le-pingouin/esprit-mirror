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
** Fichier ................: archives.php
** Description ............: 
** Date de création .......: 01/03/2001
** Dernière modification ..: 10/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));
require_once(dir_database("ids.class.php"));
require_once("archive.class.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_sArchive    = (empty($HTTP_GET_VARS["archive"]) ? NULL : $HTTP_GET_VARS["archive"]);
$url_iIdPers     = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);

$oBdd = new CBdd();

if ($url_iIdPers > 0)
{
	$oPersonne       = new CPersonne($oBdd,$url_iIdPers);
	$sPersonneNom    = $oPersonne->retNom();
	$sPersonnePrenom = $oPersonne->retPrenom();
	unset($oPersonne);
}

$sArchiveChatTelecharger = NULL;

if (isset($url_sArchive))
{
	$oIds = new CIds($oBdd,$url_iTypeNiveau,$url_iIdNiveau);
	
	$amParams = array("idForm" => $oIds->retIdForm()
		, "idActiv" => $oIds->retIdActiv());
	$sArchiveChatTelecharger = dir_chat_archives($url_iTypeNiveau,$amParams,$url_sArchive,FALSE);
	$sRepArchives            = dir_chat_archives($url_iTypeNiveau,$amParams,$url_sArchive,TRUE);
	
	$sInformations = NULL;
	$sEnteteTableau = $sMessages = NULL;
	
	$oArchive = new CArchive($sRepArchives,TRUE);
	
	$asInformations = array(
		array("Nom du salon",urldecode($oArchive->retSalon().($oArchive->retEquipe() ? " (".$oArchive->retEquipe().")" : NULL))),
		array("Date",$oArchive->retDate()." (".$oArchive->retHeureCourte().")"),
		array("D&eacute;but, fin, dur&eacute;e",$oArchive->retHeurePremierMessage()." | ".$oArchive->retHeureDernierMessage()." | ".$oArchive->retDuree()),
	);
	
	// {{{ Composer la liste des participants
	$sListeParticipants = NULL;
	
	foreach ($oArchive->asParticipants as $asParticipant)
	{
		$sParticipantPseudo     = $asParticipant[0];
		$sParticipantNomComplet = $asParticipant[1];
		
		if ($sParticipantPseudo == "-")
			continue;
		
		$sListeParticipants .= (isset($sListeParticipants) ? ", " : NULL)
				."<span"
				." title=\"{$sParticipantNomComplet}\""
				." class=\"participants\""
				.">{$sParticipantPseudo}</span>";
	}
	
	// Ajouter cette liste des participants dans les informations
	$asInformations[] = array("Participants",$sListeParticipants);
	// }}}
	
	// {{{ Afficher les informations de l'archive
	for ($i=0; $i<count($asInformations); $i++)
		$sInformations .= "<tr>"
			."<td class=\"titre_informations\" width=\"1%\" align=\"right\">"
			."<b>".str_replace(" ","&nbsp;",$asInformations[$i][0])."&nbsp;".(isset($asInformations[$i][0]) ? ":&nbsp;" : NULL)."</b>"
			."</td>"
			."<td class=\"cellule_clair\">&nbsp;".rawurldecode($asInformations[$i][1])."</td>"
			."</tr>\n";
	// }}}
		
	$sNomClass = NULL;
	
	// {{{ Afficher les messages
	foreach ($oArchive->aoMessages as $oMessage)
	{
		$bTexteGrise = FALSE;
		
		$sPseudo     = $oMessage->retPseudo();
		$sNomComplet = $oMessage->retNomComplet();
		
		if ($url_iIdPers > 0
			&& !stristr($sNomComplet,"{$sPersonneNom} {$sPersonnePrenom}")
			&& !stristr($sNomComplet,"{$sPersonnePrenom} {$sPersonneNom}"))
			$bTexteGrise = TRUE;
		
		$sMsg = str_replace("\x0A","<br>",$oMessage->retMessage());
		$sNomClass = ($sNomClass == "cellule_fonce" ? "cellule_clair" : "cellule_fonce");
		$sMessages .= "<tr>"
			."<td class=\"{$sNomClass}\" width=\"1%\" align=\"center\">"
			.($bTexteGrise ? "<span class=\"texte_grise\">".$oMessage->retHeureCourte()."</span>" : $oMessage->retHeureCourte())
			."</td>"
			."<td class=\"{$sNomClass}\" width=\"1%\" align=\"center\">"
			."<span"
			." title=\"".$oMessage->retNomComplet()."\""
			." style=\"cursor: help;\""
			.">&nbsp;"
			.($bTexteGrise ? "<span class=\"texte_grise\">{$sPseudo}</span>" : $sPseudo)
			."&nbsp;</span>"
			."</td>"
			."<td class=\"{$sNomClass}\">"
			.($bTexteGrise ? "<span class=\"texte_grise\">{$sMsg}</span>" : $sMsg)
			."</td>"
			."</tr>\n";
	}
	// }}}
	
	$sFonctionInit = "return;\n";
	
	if (isset($sMessages))
	{
		$sFonctionInit = "if (top.oTitre().changerSousTitre)\n\t\ttop.oTitre().changerSousTitre(\"".phpString2js(urldecode($oArchive->retSalon()))."\");\n";
		
		$sEnteteTableau .= "<tr>"
			."<td width=\"1%\" class=\"cellule_sous_titre\" align=\"center\">&nbsp;Heure&nbsp;</td>"
			."<td width=\"1%\" class=\"cellule_sous_titre\">&nbsp;Pseudo&nbsp;</td>"
			."<td class=\"cellule_sous_titre\">&nbsp;Message&nbsp;</td>"
			."</tr>\n";
	}
	
	include_once("archives.inc.php");
}
else
{
	include_once("archives-vide.inc.php");
}

?>

