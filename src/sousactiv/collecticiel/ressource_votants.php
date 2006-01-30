<?php

/*
** Fichier ................: ressource_votants.php
** Description ............: 
** Date de cr�ation .......: 26/11/2004
** Derni�re modification ..: 07/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();
$oProjet->initEquipe(TRUE);

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdResSA  = (empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);
$url_aiIdPers  = (empty($HTTP_GET_VARS["idPers"]) ? NULL : $HTTP_GET_VARS["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$g_bPeutEvaluer = $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL");

// ---------------------
// Appliquer les changements
// ---------------------
if ($g_bPeutEvaluer && is_array($url_aiIdPers))
{
	$oProjet->oSousActivCourante->initEquipe($url_iIdEquipe,TRUE);
	
	foreach ($url_aiIdPers as $iIdPers)
		$oProjet->oSousActivCourante->voterPourRessource($url_iIdResSA,$iIdPers);
}

$oRSA = new CRessourceSousActiv($oProjet->oBdd,$url_iIdResSA);
$iNbVotants = $oRSA->initVotants();

$oRSA->initEquipe(TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ressource_votants.tpl");

$oBlocTableVotants          = new TPL_Block("BLOCK_TABLE_VOTANTS",$oTpl);
$oBlocTableVotantsManquants = new TPL_Block("BLOCK_TABLE_VOTANTS_MANQUANTS",$oTpl);
$oBlocPasVotantTrouve       = new TPL_Block("BLOCK_PAS_VOTANT",$oTpl);

$sSetSexe = array(
	$oTpl->defVariable("SET_SEXE_FEMININ")
	, $oTpl->defVariable("SET_SEXE_MASCULIN"));

$sSetCourriel = array (
	$oTpl->defVariable("SET_SANS_COURRIEL")
	, $oTpl->defVariable("SET_COURRIEL"));

if ($iNbVotants > 0)
{
	$aiIdVotants = array();
	
	$oBlocVotant = new TPL_Block("BLOCK_VOTANT",$oBlocTableVotants);
	$oBlocVotant->beginloop();
	
	foreach ($oRSA->aoVotants as $oVotant)
	{
		$sEmail        = $oVotant->retEmail();
		$sIcones       = $sSetCourriel[emailValide($sEmail)];
		$aiIdVotants[] = $oVotant->retId();
		
		$oBlocVotant->nextloop();
		
		$oBlocVotant->remplacer("{personne.sexe}",$sSetSexe[($oVotant->retSexe() == "M")]);
		$oBlocVotant->remplacer("{personne.nom}",$oVotant->retNom());
		$oBlocVotant->remplacer("{personne.prenom}",$oVotant->retPrenom());
		$oBlocVotant->remplacer("{outil.courriel}",$sIcones);
		$oBlocVotant->remplacer("{personne.pseudo}",$oVotant->retPseudo());
		$oBlocVotant->remplacer("{personne.courriel}",$sEmail);
	}
	
	if ($g_bPeutEvaluer
		&& $oRSA->oEquipe->retNbMembres() > $iNbVotants)
	{
		$oBlocMembreNonVotant = new TPL_Block("BLOCK_VOTANT_MANQUANT",$oBlocTableVotantsManquants);
		$oBlocMembreNonVotant->beginLoop();
		
		foreach ($oRSA->oEquipe->aoMembres as $oMembre)
		{
			$iIdPers = $oMembre->retId();
			
			if (in_array($iIdPers,$aiIdVotants))
				continue;
			
			$sEmail  = $oMembre->retEmail();
			$sIcones = $sSetCourriel[emailValide($sEmail)];
			
			$oBlocMembreNonVotant->nextLoop();
			
			$oBlocMembreNonVotant->remplacer("{personne.id}",$iIdPers);
			$oBlocMembreNonVotant->remplacer("{personne.sexe}",$sSetSexe[($oMembre->retSexe() == "M")]);
			$oBlocMembreNonVotant->remplacer("{personne.nom}",$oMembre->retNom());
			$oBlocMembreNonVotant->remplacer("{personne.prenom}",$oMembre->retPrenom());
			$oBlocMembreNonVotant->remplacer("{outil.courriel}",$sIcones);
			$oBlocMembreNonVotant->remplacer("{personne.pseudo}",$oMembre->retPseudo());
			$oBlocMembreNonVotant->remplacer("{personne.courriel}",$sEmail);
		}
		
		$oBlocMembreNonVotant->afficher();
		
		$oBlocTableVotantsManquants->remplacer(array("{ressource.id}","{equipe.id}"), array($url_iIdResSA,$url_iIdEquipe));
		$oBlocTableVotantsManquants->afficher();
	}
	else
		$oBlocTableVotantsManquants->effacer();
	
	$oBlocVotant->afficher();
	$oBlocTableVotants->afficher();
	
	$oBlocPasVotantTrouve->effacer();
}
else
{
	$oBlocTableVotants->effacer();
	$oBlocPasVotantTrouve->afficher();
	
	$oBlocTableVotantsManquants->effacer();
}

$oTpl->afficher();

$oProjet->terminer();

?>

