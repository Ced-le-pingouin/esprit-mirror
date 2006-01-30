<?php

/*
** Fichier ................: choix_formation.php
** Description ............: 
** Date de cr�ation .......: 18/09/2002
** Derni�re modification ..: 04/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initStatutsUtilisateur(FALSE);
$oProjet->verifPeutUtiliserOutils();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdForm = (empty($HTTP_GET_VARS["idForm"]) ? 0 : $HTTP_GET_VARS["idForm"]);
$url_sFiltre = (empty($HTTP_GET_VARS["filtre"]) ? NULL : $HTTP_GET_VARS["filtre"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("choix_formation.tpl");

$oBlocMessage            = new TPL_Block("BLOCK_MESSAGE",$oTpl);
$sVarSansFormation       = $oBlocMessage->defVariable("VAR_SANS_FORMATION");
$sVarSelectionnerMessage = $oBlocMessage->defVariable("VAR_SELECTIONNER_FORMATION");

$oBlocListeFormations  = new TPL_Block("BLOCK_LISTE_FORMATIONS",$oTpl);
$sVarFormation         = $oBlocListeFormations->defVariable("VAR_FORMATION");
$sVarFormationActuelle = $oBlocListeFormations->defVariable("VAR_FORMATION_ACTUELLE");

// ---------------------
// Rechercher toutes les formations du responsable de formation
// ---------------------
$sMessage = NULL;
$sListeFormations = NULL;

if (($iNbrFormations = $oProjet->initFormationsUtilisateur()) > 0)
{
	$asRechercher = array("{formation.id}","{formation.nom}");
	
	if ($url_iIdForm < 1)
		$url_iIdForm = $oProjet->oFormationCourante->retId();
	
	$sMessage = &$sVarSelectionnerMessage;
	
	foreach ($oProjet->aoFormations as $oFormation)
	{
		$sNomFormation = $oFormation->retNom();
		
		if (isset($url_sFiltre))
		{
			if (stristr($sNomFormation,$url_sFiltre) === FALSE)
				continue;
			else
				$sNomFormation = eregi_replace("($url_sFiltre)([:alnum:]*)","<span style='background-color: rgb(0,255,0);'>\\1</span>\\2",$sNomFormation);
		}
		
		$iIdForm = $oFormation->retId();
		
		$amRemplacer = array(
			$iIdForm
			, $sNomFormation
				.($url_iIdForm == $iIdForm ? $sVarFormationActuelle : NULL));
		
		$sListeFormations .= str_replace($asRechercher,$amRemplacer,$sVarFormation);
	}
}

// Afficher un message d'erreur si la plate-forme n'a pas trouv� de formation
if (empty($sListeFormations))
	$sMessage = &$sVarSansFormation;

$oBlocMessage->remplacer("{message}",$sMessage);
$oBlocListeFormations->remplacer("{liste_formations}",$sListeFormations);

$oBlocMessage->afficher();
$oBlocListeFormations->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

