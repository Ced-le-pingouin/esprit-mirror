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

define("NUM_PORT_AWARENESS","2600");

function retAwarenessJavascript ()
{
	return "<script"
		." language=\"javascript\""
		." type=\"text/javascript\""
		." src=\"".dir_javascript("awareness.js")."\">"
		."</script>";
}

function retNomUniqueAwareness ()
{
	global $oProjet;
	
	$iIdForm = 0;
	
	if (isset($oProjet->oFormationCourante) &&
		is_object($oProjet->oFormationCourante))
		$iIdForm = $oProjet->oFormationCourante->retId();
	
	return "{$oProjet->sNomRep}_{$iIdForm}";
}

function retAwarenessApplet ($v_sLocation)
{
	return NULL; // Desactiver l'awareness
	
	global $oProjet;
	
	$sChemin = dir_admin("awareness/client",NULL,FALSE);
	
	if (!stristr($_SERVER["HTTP_USER_AGENT"],"Netscape") &&
		isset($oProjet->oUtilisateur))
		return "<applet"
			." name=\"AwarenessApplet\""
			." width=\"29\" height=\"17\""
			." codebase=\"{$sChemin}\""
			." code=\"AwarenessApplet.class\""
			." archive=\"AwarenessClient.jar\""
			." MAYSCRIPT"
			.">\n"
			."<param name=\"location\" value=\"{$v_sLocation}\">\n"
			."<param name=\"id_session\" value=\"".retNomUniqueAwareness()."\">\n"
			."<param name=\"host\" value=\"".$_SERVER["SERVER_ADDR"]."\">\n"
			."<param name=\"port\" value=\"".NUM_PORT_AWARENESS."\">\n"
			."<param name=\"nickname\" value=\"".$oProjet->oUtilisateur->retPseudo()."\">\n"
			."<param name=\"locale\" value=\"Fra\">\n"
			."</applet>\n";
	else
		return "<img"
			." src=\"{$sChemin}/non_oeil.jpg\""
			." width=\"29\" height=\"17\""
			." border=\"0\""
			." title=\"Awareness non disponible avec les Netscape \""
			.">\n";
}

function retAwarenessSpy ($v_sLocation,$v_bTraduire=TRUE) 
{
	global $oProjet, $_SERVER;
	
	$sChemin = dir_admin("awareness/client",NULL,FALSE);
	
	if (!stristr($_SERVER["HTTP_USER_AGENT"],"Netscape") &&
		isset($oProjet->oUtilisateur))
			return "<applet"
			." name=\"AwarenessSpyClient\""
			." width=\"1\" height=\"1\""
			." codebase=\"{$sChemin}\""
			." code=\"AwarenessSpyEsprit.class\""
			.">\n" // <applet ...>
			."<param name=\"title_list_connected\" value=\"".urlencode(stripslashes($v_sLocation))."\">\n"
			."<param name=\"session\" value=\"".retNomUniqueAwareness()."\">\n"
			."<param name=\"statut_utilisateur\" value=\"".urlencode($oProjet->retTexteStatutUtilisateur())."\">\n"
			."</applet>\n";
	
	return NULL;
}

?>
