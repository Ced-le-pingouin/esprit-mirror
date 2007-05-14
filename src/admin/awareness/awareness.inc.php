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

function retNomUniqueAwareness ()
{
	global $oProjet;
	
	$iIdForm = 0;
	
	if (isset($oProjet->oFormationCourante) &&
		is_object($oProjet->oFormationCourante))
		$iIdForm = $oProjet->oFormationCourante->retId();
	
	return "{$oProjet->sNomRep}_{$iIdForm}";
}

function retAwarenessSpy ($v_sLocation, $v_bTraduire = TRUE)
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
