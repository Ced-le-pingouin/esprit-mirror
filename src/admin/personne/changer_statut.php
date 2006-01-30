<?php

/*
** Fichier ................: changer_statut.php
** Description ............: Changer de statut de l'utilisateur
** Date de cr�ation .......: 27/02/2002
** Derni�re modification ..: 20/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Gestion
// ---------------------
$sParamsRechargerFenParent = NULL;

if (!empty($HTTP_POST_VARS["idStatut"]))
{
	$aamNiveaux = array(
		array(&$oProjet->oFormationCourante,"idForm")
		, array(&$oProjet->oModuleCourant,"idMod")
		, array(&$oProjet->oRubriqueCourante,"idUnite")
		, array(&$oProjet->oActivCourante,"idActiv")
		, array(&$oProjet->oSousActivCourante,"idSousActiv"));
	
	foreach ($aamNiveaux as $amNiveau)
		$sParamsRechargerFenParent .= (isset($sParamsRechargerFenParent) ? "&" : "?")
			.$amNiveau[1]."=".(is_object($amNiveau[0]) ? $amNiveau[0]->retId() : 0);
	
	$oProjet->changerStatutUtilisateur($HTTP_POST_VARS["idStatut"],TRUE);
	
	// Nous devons mettre � jour la fen�tre parente
	// et fermer absolument cette fen�tre car elle sera orpheline.
	echo "<html>"
		."<header>"
		."<script type=\"text/javascript\" language=\"javascript\">"
		."<!--\n"
		."top.opener.recharger('{$sParamsRechargerFenParent}');"
		."top.close();"
		."\n//-->"
		."</script>"
		."</header>"
		."<body>"
		."</body>"
		."</html>\n";
		
	exit();
}

// ---------------------
// Initialiser
// ---------------------
$iIdPers = $oProjet->retIdUtilisateur();
$iReelStatutUtilisateur = $oProjet->retReelStatutUtilisateur();

$iIdForm = 0;
$iIdMod = 0;
$bInscritAutoModules = TRUE;

if (is_object($oProjet->oRubriqueCourante))
	if (is_object($oProjet->oFormationCourante))
	{
		$iIdForm = $oProjet->oFormationCourante->retId();
		$bInscritAutoModules = $oProjet->oFormationCourante->retInscrAutoModules();
		
		if (isset($oProjet->oModuleCourant) && is_object($oProjet->oModuleCourant))
			$iIdMod = $oProjet->oModuleCourant->retId();
	}

$oStatutUtilisateur = new CStatutUtilisateur($oProjet->oBdd,$iIdPers);
$oStatutUtilisateur->initStatuts($iIdForm,$iIdMod,$bInscritAutoModules);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("changer_statut.tpl",FALSE,TRUE));

$oBlocListeStatuts = new TPL_Block("BLOCK_LISTE_STATUTS",$oTpl);
$oBlocListeStatuts->beginLoop();

for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
	if ($oStatutUtilisateur->aiStatuts[$iIdxStatut])
	{
		$bStatutActuel = ($iReelStatutUtilisateur == $iIdxStatut);
		$sStatutActuel = $oProjet->retTexteStatutUtilisateur($iIdxStatut);
		
		$oBlocListeStatuts->nextLoop();
		
		$oBlocListeStatuts->remplacer("{nom_radio_statut}","idStatut");
		$oBlocListeStatuts->remplacer("{valeur_statut}",$iIdxStatut);
		$oBlocListeStatuts->remplacer("{selectionner_statut}",($bStatutActuel ? " checked": NULL));
		$oBlocListeStatuts->remplacer("{nom_statut}",$sStatutActuel.($bStatutActuel ? "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" border=\"0\">": NULL));
	}

$oBlocListeStatuts->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

