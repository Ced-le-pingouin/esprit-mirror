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

function description ($v_iNum,$sIntitule)
{
	global $oProjet, $g_bModifier;
	
	return "<tr>"
		."<td class=\"intitule\" width=\"1%\">{$sIntitule}&nbsp;:</td>"
		."<td>"
		."<textarea rows=\"5\" cols=\"50\" name=\"DESCRIPTION[$v_iNum]\""
		." style=\"width: 100%;\""
		.($g_bModifier ? NULL : " disabled")
		.">".$oProjet->oSousActivCourante->retDescr()."</textarea>"
		."</td>"
		."</tr>";
}

function div_forum ()
{
	global $oProjet, $g_iSousActiv, $g_bModifier;
	
	$oForum = new CForum($oProjet->oBdd);
	$oForum->initForumParType(TYPE_SOUS_ACTIVITE,$g_iSousActiv);
	
	$sOptionsModalites = NULL;
	
	$aaModalites = $oForum->retListeModalites();
	
	foreach ($aaModalites as $aModalite)
		$sOptionsModalites .= "<option"
			." value=\"{$aModalite[0]}\""
			.($aModalite[2] ? " selected" : NULL)
			.">{$aModalite[1]}</option>";
	
	$sHtmlBaliseSelect = "<select"
		." name=\"MODALITE[".LIEN_FORUM."]\""
		.($g_bModifier ? NULL : " disabled")
		.">{$sOptionsModalites}</select>";
	
	return "<!-- :DEBUT: Forum -->"
		."<div id=\"lien_forum\" class=\"Cacher\">"
		."<fieldset>"
		."<legend>"
		."&nbsp;Forum&nbsp;"
		."&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"return forum('?idNiveau={$g_iSousActiv}&typeNiveau=".TYPE_SOUS_ACTIVITE."','WinForumSA{$g_iSousActiv}')\" onfocus=\"blur()\">Modifier</a>&nbsp;]&nbsp;"
		."</legend>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">"
		."<tr>"
		."<td><div class=\"intitule\">Modalit&eacute;&nbsp;:</div></td>"
		."<td width=\"99%\">{$sHtmlBaliseSelect}</td>"
		."</tr>\n"
		."<tr>"
		."<td colspan=\"2\" align=\"right\">"
		."<input"
		." type=\"checkbox\""
		." name=\"ACCESSIBLE_VISITEURS[".LIEN_FORUM."]\""
		.($oForum->retAccessibleVisiteurs() ? " checked" : NULL)
		.($g_bModifier ? NULL : " disabled")
		.">&nbsp;&nbsp;Accessible aux visiteurs</td>"
		."</tr>"
		."</table>"
		."</fieldset>"
		."</div>"
		."<!-- :FIN: Forum -->\n";
}

function div_galerie ()
{
	global $oProjet, $g_bModifier;
	
	$sCollecticielsAssocies = htmlentities("Collecticiels associés",ENT_COMPAT,"UTF-8");
	
	if (($iNbrCollecticiels = $oProjet->oRubriqueCourante->initCollecticiels()) > 0)
	{
		$oGalerie = new CGalerie ($oProjet->oBdd,$oProjet->oSousActivCourante->retId());
		$iNbCollecticielsAssocies = $oGalerie->initCollecticiels();
	}
	
	$sPremierCollecticiel = "&nbsp;";
	$sAutresCollecticiels = NULL;
	
	for ($i=0; $i<$iNbrCollecticiels; $i++)
	{
		if (isset ($sCollecticiels))
			$sCollecticiels .= "<br>";
		
		$iId = $oProjet->oRubriqueCourante->aoCollecticiels[$i]->retId();
		
		if ($i > 0)
			$sAutresCollecticiels .= "<tr>"
				."<td>&nbsp;</td>"
				."<td>"
				."<input type=\"checkbox\" name=\"COLLECTICIEL[]\""
				." value=\"$iId\""
				.($iNbCollecticielsAssocies == 0 || $oGalerie->estAssocierGalerie($iId) ? " checked" : NULL)
				.($g_bModifier ? NULL : " disabled")
				.">&nbsp;&nbsp;"
				.$oProjet->oRubriqueCourante->aoCollecticiels[$i]->retNom()
				."</td>"
				."</tr>\n";
		else
			$sPremierCollecticiel = "<input type=\"checkbox\" name=\"COLLECTICIEL[]\""
				." value=\"$iId\""
				.($iNbCollecticielsAssocies == 0 || $oGalerie->estAssocierGalerie($iId) ? " checked" : NULL)
				.($g_bModifier ? NULL : " disabled")
				.">&nbsp;&nbsp;"
				.$oProjet->oRubriqueCourante->aoCollecticiels[$i]->retNom();
	}
	
	if (!isset ($sCollecticiels))
		$sCollecticiels = "<tr><td>&nbsp;</td><td>Pas de collecticiel trouv&eacute;</td></tr>";
	
	$sCollecticiels .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	
	$sConteneur = "<!-- :DEBUT: Galerie -->\n"
		."<div id=\"lien_galerie\" class=\"Cacher\">"
		."<fieldset>"
		."<legend>&nbsp;Galerie&nbsp;</legend>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\">"
		.entrerDescription("DESCRIPTION[".LIEN_GALERIE."]",$oProjet->oSousActivCourante->retDescr(),"Consigne",urlencode(addslashes($oProjet->oSousActivCourante->retNom())),FALSE)
		."<tr>"
		."<td width=\"1%\" nowrap=\"nowrap\"><span class=\"intitule\">{$sCollecticielsAssocies}&nbsp;:</span></td>"
		."<td>{$sPremierCollecticiel}</td>"
		."</tr>\n"
		.$sAutresCollecticiels
		."</table>"
		."</fieldset>"
		."</div>\n"
		."<!-- :FIN: Galerie -->\n";
	
	return $sConteneur;
}

function div_glossaire ()
{
	global $oProjet, $g_bModifier, $g_iSousActiv;
	
	$oProjet->oFormationCourante->initGlossaires();
	
	$sListeGlossaires = NULL;
	
	foreach ($oProjet->oFormationCourante->aoGlossaires as $oGlossaire)
	{
		$sListeGlossaires .= "<option"
			." value=\"".$oGlossaire->retId()."\""
			.($oGlossaire->associerSousActiv($g_iSousActiv) ? " selected" : NULL)
			.">".htmlentities($oGlossaire->retTitre(),ENT_COMPAT,"UTF-8")."</option>";
	}
	
	if (isset($sListeGlossaires))
		$sListeGlossaires = "<option value=\"0\">Choisissez une composition de glossaire</option>"
			.$sListeGlossaires;
	else
		$sListeGlossaires = "<option value=\"0\">Pas de composition de glossaire trouvé</option>";
	
	$sIntitule = htmlentities("Glossaire associés au ".strtolower(INTITULE_SOUS_ACTIV),ENT_COMPAT,"UTF-8");
	
	$sConteneur = "<!-- :DEBUT: Glossaire -->\n"
		."<div id=\"lien_glossaire\" class=\"Cacher\">"
		."<fieldset>"
		."<legend>&nbsp;Glossaire&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"composer_glossaire('".$oProjet->oFormationCourante->retId()."')\" onfocus=\"blur()\">composer</a>&nbsp;]&nbsp;</legend>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\">"
		.entrerDescription("DESCRIPTION[".LIEN_GLOSSAIRE."]",$oProjet->oSousActivCourante->retDescr(),"Description",urlencode(addslashes($oProjet->oSousActivCourante->retNom())),FALSE)
		."<tr>"
		."<td width=\"1%\" colspan=\"2\"><span class=\"intitule\">{$sIntitule}&nbsp;:</span></td>"
		."</tr>"
		."<tr><td>&nbsp;</td><td><select name=\"ID_GLOSSAIRE\">$sListeGlossaires</select></td></tr>"
		."</tr>"
		."</table>"
		."</fieldset>"
		."</div>\n"
		."<!-- :FIN: Glossaire -->\n";
	
	return $sConteneur;
}

?>
