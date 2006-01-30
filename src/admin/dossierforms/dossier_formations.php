<?php

/*
** Fichier ................: dossier_formations-index.php
** Description ............:
** Date de création .......: 04/04/2005
** Dernière modification ..: 29/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("dossiers.lang"));
require_once(dir_database("dossier_formations.tbl.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdDossierForms  = (empty($HTTP_GET_VARS["idDossierForms"]) ? $oProjet->asInfosSession[SESSION_DOSSIER_FORMS] : $HTTP_GET_VARS["idDossierForms"]);
$url_aiIdForms        = (empty($HTTP_GET_VARS["idForms"]) ? array() : $HTTP_GET_VARS["idForms"]);
$url_aiIdDossierForms = (empty($HTTP_GET_VARS["ordreForms"]) ? NULL : $HTTP_GET_VARS["ordreForms"]);
$url_sEvent           = (empty($HTTP_GET_VARS["event"]) ? NULL : $HTTP_GET_VARS["event"]);

// ---------------------
// Appliquer les changements
// ---------------------
if ("sauver" == $url_sEvent)
{
	$aaDossierForms_Form = array();
	
	foreach ($url_aiIdForms as $iIdForm)
		$aaDossierForms_Form[] = array(
			"IdForm" => $iIdForm
			, "OrdreForm" => $url_aiIdDossierForms[$iIdForm]
		);
	
	$oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);
	
	if (count($aaDossierForms_Form) > 0)
		$oDossierForms->ajouterFormations($aaDossierForms_Form);
	else
		$oDossierForms->effacerFormations();
}

// ---------------------
// Initialiser
// ---------------------
$iNbFormations = $oProjet->initFormationsUtilisateur(FALSE,FALSE);

$aiIdForms = array();

foreach ($oProjet->aoFormations as $oFormation)
	$aiIdForms[] = $oFormation->retId();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("dossier_formations.tpl");

$oBlocFormation = new TPL_Block("BLOCK_FORMATION",$oTpl);

if ($iNbFormations > 0)
{
	$iIdxForm = 0;
	$sDesactiver = ($url_iIdDossierForms > 0 ? NULL : " disabled=\"disabled\"");
	
	$oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);
	$oDossierForms->initFormations($aiIdForms,FALSE);
	
	$asTplRepl = array("{formation.index}","{formation.id}","{formation.nom}","{formation.visible}","{input.attributes}","{select.attributes}");
	
	$oBlocFormation->beginLoop();
	
	foreach ($oDossierForms->aoFormations as $oFormation)
	{
		$oBlocFormation->nextLoop();
		
		$oBlocOptionOrdre = new TPL_Block("OPTION_FORMATION_ORDRE",$oBlocFormation);
		$oBlocOptionOrdre->beginLoop();
		
		$iOrdre = $oFormation->retNumOrdre();
		
		for ($i=1; $i<=$iNbFormations; $i++)
		{
			$oBlocOptionOrdre->nextLoop();
			$oBlocOptionOrdre->remplacer(array("{formation.ordre}","{option.attributes}"),array($i,(($iIdxForm+1) == $i) ? " selected=\"selected\"" : NULL));
		}
		
		$oBlocOptionOrdre->afficher();
		
		$oBlocFormation->remplacer($asTplRepl,array($iIdxForm++,$oFormation->retId(),$oFormation->retNom(),'1',($iOrdre != 32635 ? " checked=\"checked\"" : NULL).$sDesactiver,$sDesactiver));
		$oBlocFormation->cycle();
	}
	
	$oBlocFormation->afficher();
}

// {{{ Formulaire
$oTpl->remplacer("{dossier_formations.id}",$url_iIdDossierForms);
$oTpl->remplacer("{inputs.action.value}",$url_sEvent);
// }}}

$oBlocNomDossier = new TPL_Block("BLOCK_NOM_DOSSIER",$oTpl);

if ($url_iIdDossierForms > 0)
{
	$oBlocNomDossier->remplacer("{dossier.nom}",htmlentities($oDossierForms->retNom()));
	$oBlocNomDossier->afficher();
}
else
	$oBlocNomDossier->effacer();

// {{{ Traduction
$oTpl->remplacer("[TITRE_CREER_MODIFIER_DOSSIER]",TITRE_CREER_MODIFIER_DOSSIER);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

