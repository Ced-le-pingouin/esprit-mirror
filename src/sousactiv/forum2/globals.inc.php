<?php

require_once("../../globals.inc.php");
require_once(dir_database("ids.class.php"));

function dir_forum_ressources (&$v_oIds,$v_iTypeNiveau,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	switch ($v_iTypeNiveau)
	{
		case TYPE_ACTIVITE:
		case TYPE_SOUS_ACTIVITE: return dir_cours($v_oIds->retIdActiv(),$v_oIds->retIdForm(),"forum/{$v_sFichierAInclure}",$v_bCheminAbsolu);
		case TYPE_RUBRIQUE: return dir_formation($v_oIds->retIdForm(),"forum/{$v_sFichierAInclure}",$v_bCheminAbsolu);
	}
	return NULL;
}

?>
