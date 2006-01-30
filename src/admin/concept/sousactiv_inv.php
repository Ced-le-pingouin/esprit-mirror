<?php

/*
** Fichier ................: sousactiv_inv.php
** Description ............:
** Date de création .......: 16/11/2005
** Dernière modification ..: 24/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

if ($oProjet->retStatutUtilisateur() > STATUT_PERS_TUTEUR)
	exit();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSousActiv = $HTTP_GET_VARS["idSousActiv"];

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,TYPE_SOUS_ACTIVITE,$url_iIdSousActiv);

$oFormation = new CFormation($oProjet->oBdd,$oIds->retIdForm());
$oModule    = new CModule($oProjet->oBdd,$oIds->retIdMod());
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

// {{{ Appliquer les changements
if (isset($HTTP_POST_VARS["appliquer"]) > 0)
	$oSousActiv->ajouterInscritsNonAutorises($HTTP_POST_VARS["idPers"]);
// }}}

// {{{ Rechercher les inscrits
$aoInscrits = array();

if ($oFormation->retInscrAutoModules())
{
	if ($oFormation->initInscrits() > 0)
		$aoInscrits = &$oFormation->aoInscrits;
}
else if ($oModule->initInscrits() > 0)
	$aoInscrits = &$oModule->aoInscrits;

$iNbInscrits = count($aoInscrits);
// }}}

// {{{ Rechercher les inscrits non autorisés
$oSousActiv->initInscritsNonAutorises();

$aiIdsInscritsNonAutorises = array();

foreach ($oSousActiv->aoInscritsNonAutorises as $oInscritNonAutorise)
	$aiIdsInscritsNonAutorises[] = $oInscritNonAutorise->retId();
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sousactiv_inv.tpl");

$oBlocInscrit = new TPL_Block("BLOCK_PERSONNE",$oTpl);

$sSetAucunInscrit = $oBlocInscrit->defVariable("SET_ELSE_PERSONNE");

if ($iNbInscrits > 0)
{
	$asRechTpl = array(
		"{personne.id}",
		"{personne.nom}",
		"{personne.prenom}",
		"{personne.checked}"
	);
	
	$oBlocInscrit->beginLoop();
	
	foreach ($aoInscrits as $oInscrit)
	{
		$iIdPers = $oInscrit->retId();
		
		$amReplTpl = array(
			$iIdPers,
			strtoupper($oInscrit->retNom()),
			$oInscrit->retPrenom(),
			(in_array($iIdPers,$aiIdsInscritsNonAutorises) ? NULL : " checked=\"checked\"")
		);
		
		$oBlocInscrit->nextLoop();
		$oBlocInscrit->remplacer($asRechTpl,$amReplTpl);
	}
}
else
	$oBlocInscrit->defDonnees($sSetAucunInscrit);

$oBlocInscrit->afficher();

// {{{ Global
$oTpl->remplacer("{sousactiv.id}",$url_iIdSousActiv);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

