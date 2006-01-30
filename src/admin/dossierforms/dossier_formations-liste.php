<?php

/*
** Fichier ................: dossier_formations-index.php
** Description ............:
** Date de création .......: 04/04/2005
** Dernière modification ..: 25/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("dossier_formations.tbl.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdDossierForms = (empty($HTTP_GET_VARS["idDossierForms"]) ? $oProjet->asInfosSession[SESSION_DOSSIER_FORMS] : $HTTP_GET_VARS["idDossierForms"]);

// ---------------------
// Initialiser
// ---------------------
$g_oDossierForms = new CDossierForms($oProjet->oBdd);
$g_oDossierForms->initDossierForms($oProjet->retIdUtilisateur(),TRUE);

$g_asRechTpl = array("{dossier_formation.id}","{dossier_formation.nom}","{dossier_formation.icone}");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("dossier_formations-liste.tpl");

$oBlocDossierFormations = new TPL_Block("BLOCK_DOSSIER_FORMATIONS",$oTpl);

$asSetIcones = array(
	"avec" => $oTpl->defVariable("SET_ICONE_AVEC_FORMATIONS")
	, "sans" => $oTpl->defVariable("SET_ICONE_AUCUNE_FORMATION")
	, "premier" => $oTpl->defVariable("SET_ICONE_PREMIER_DOSSIER")
);

$asSetDossier = array(
	"sans" => $oTpl->defVariable("SET_SANS_DOSSIER")
	, "avec" => $oTpl->defVariable("SET_DOSSIER")
);

if (count($g_oDossierForms->aoDossierForms) > 0)
{
	$oBlocDossierFormations->remplacer("{dossier}",$asSetDossier["avec"]);
	
	$oBlocDossierFormations->beginLoop();
	
	foreach ($g_oDossierForms->aoDossierForms as $oDossierForms)
	{
		if ($url_iIdDossierForms < 1)
			$url_iIdDossierForms = $oDossierForms->retId();
		
		if ($oDossierForms->retPremierDossier())
			$sIcone = $asSetIcones["premier"];
		else if (count($oDossierForms->aoFormations) > 0)
			$sIcone = $asSetIcones["avec"];
		else
			$sIcone = $asSetIcones["sans"];
		
		$oBlocDossierFormations->nextLoop();
		$oBlocDossierFormations->remplacer($g_asRechTpl,array($oDossierForms->retId(),htmlentities($oDossierForms->retNom()),$sIcone));
	}
}
else
	$oBlocDossierFormations->remplacer("{dossier}",$asSetDossier["sans"]);

$oBlocDossierFormations->afficher();

$oTpl->remplacer("{dossier_formations.id}",$url_iIdDossierForms);

$oTpl->afficher();

$oProjet->terminer();

?>

