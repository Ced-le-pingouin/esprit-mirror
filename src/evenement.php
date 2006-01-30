<?php

require_once("globals.inc.php");

$url_bMAJ = (isset($HTTP_GET_VARS["maj"]) ? $HTTP_GET_VARS["maj"] : FALSE);

if ($url_bMAJ)
	$oProjet = new CProjet();

if (isset($oProjet->oUtilisateur))
	$oProjet->ecrireEvenement(TYPE_EVEN_DECONNEXION);

if (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante))
{
	include_once(dir_database("evenement.tbl.php"));
	$oEvenDetail = new CEvenement_Detail($oProjet->oBdd,$oProjet->retNumeroUniqueSession(),$oProjet->oFormationCourante->retId());
	$oEvenDetail->sortirFormation();
	$oEvenDetail = NULL;
}

?>