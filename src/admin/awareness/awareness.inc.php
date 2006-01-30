<?php

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
	
	global $oProjet, $HTTP_SERVER_VARS;
	
	$sChemin = dir_admin("awareness/client",NULL,FALSE);
	
	if (!stristr($HTTP_SERVER_VARS["HTTP_USER_AGENT"],"Netscape") &&
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
			."<param name=\"host\" value=\"".$HTTP_SERVER_VARS["SERVER_ADDR"]."\">\n"
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
	global $oProjet, $HTTP_SERVER_VARS;
	
	$sChemin = dir_admin("awareness/client",NULL,FALSE);
	
	if (!stristr($HTTP_SERVER_VARS["HTTP_USER_AGENT"],"Netscape") &&
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
