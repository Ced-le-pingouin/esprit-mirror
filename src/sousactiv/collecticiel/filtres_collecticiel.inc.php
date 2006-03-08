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

function construireHtmlSelect ($v_sNomSelect,$v_sOnChange,$v_amListe,$v_mSelect)
{
	$sHtmlSelect = "<select"
		." name=\"{$v_sNomSelect}\""
		." onchange=\"{$v_sOnChange}\""
		.">";
	
	for ($i=0; $i<count($v_amListe); $i++)
		$sHtmlSelect .= "<option"
			." value=\"".$v_amListe[$i][0]."\""
			.($v_amListe[$i][0] == $v_mSelect ? " selected" : NULL)
			.">".$v_amListe[$i][1]."</option>";
	
	$sHtmlSelect .= "</select>\n";
	
	return $sHtmlSelect;
}

// ----------------------
// STATUTS
// ----------------------
$amStatuts = array(
		array(0,"Tous les documents"),
		array(STATUT_RES_EVALUEE,"Evalué"),
		array(STATUT_RES_ACCEPTEE,"Accepté"),
		array(STATUT_RES_APPROF,"Approfondir"),
		array(STATUT_RES_SOUMISE,"Soumis pour évaluation"),
		array(STATUT_RES_EN_COURS,"En cours"),
		array(STATUT_RES_TRANSFERE,"Transféré"));

$sHtmlSelectStatut = construireHtmlSelect("SELECT_STATUT","document.forms[0].submit()",$amStatuts,$g_iStatut);

// ----------------------
// PERSONNES/EQUIPES
// ----------------------
$asListes = array(NULL);

for ($i=0; $i<count($asNomsEspaces); $i++)
	$asListes[] = array($aiIdsEspaces[$i],$asNomsEspaces[$i]);

if ($iModalite == MODALITE_INDIVIDUEL)
	$asListes[0] = array(0,($i > 0 ? "Tous les étudiants" : "Pas d'étudiant trouvé"));
else
	$asListes[0] = array(0,($i > 0 ? "Toutes les équipes" : "Pas d'équipe trouvée"));

$sHtmlSelectPersonne = construireHtmlSelect("SELECT_PERSONNE","document.forms[0].submit()",$asListes,$g_iIdPers);

// ----------------------
// DATES
// ----------------------
$asDate = array(
		array("0","Tous les jours"),
		array(date("Y-m-d"),"Aujourd'hui"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))),"Depuis hier"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y"))),"Depuis 2 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-3,date("Y"))),"Depuis 3 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-4,date("Y"))),"Depuis 4 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-5,date("Y"))),"Depuis 5 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y"))),"Depuis 6 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y"))),"Depuis 1 semaine"),
		array(date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y"))),"Depuis 1 mois"));

$sHtmlSelectDate = construireHtmlSelect("SELECT_DATE","document.forms[0].submit()",$asDate,$g_sDate);

$sHtmlCheckboxBloc = "<br>"
	."<input type=\"checkbox\" name=\"CB_AFF_BLOC\""
	." onchange=\"document.forms[0].submit()\""
	." onclick=\"blur()\""
	.($g_bBloc ? " checked" : NULL)
	.">Afficher les blocs vides";

?>

