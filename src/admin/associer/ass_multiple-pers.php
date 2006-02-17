<?php

/*
** Fichier ................: ass_multiple-pers.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 09/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_INSCRIPTION");

$url_iIdForm   = $HTTP_GET_VARS["ID_FORM"];
$url_iIdStatut = $HTTP_GET_VARS["STATUT_PERS"];
$url_iModeTri  = TRI_CROISSANT;

// ---------------------
// Inscrire des personnes à un ou plusieurs cours
// ---------------------
if (!empty($HTTP_POST_VARS))
{
	if ($HTTP_POST_VARS["ACTION"] == "ajouter")
	{
		if (isset($HTTP_POST_VARS["ID_PERS"]))
		{
			// Récupérer les ids des modules
			$aiIdMod = explode(",",$HTTP_POST_VARS["IDS_ACTION"]);
			
			// Pour chaque module inscrire les nouvelles personnes
			foreach ($aiIdMod as $iIdMod)
			{
				$oModule = new CModule($oProjet->oBdd,$iIdMod);
				$oModule->inscrirePersonnes($HTTP_POST_VARS["ID_PERS"],$url_iIdStatut);
			}
		}
	}
	else if ($HTTP_POST_VARS["ACTION"] == "retirer")
	{
		// 1:6,79,52;2:12,15
		// + + +     +    +-- id de la troisième personne
		// + + +     +-- id du deuxième module
		// + + +
		// + + +-- id de la deuxième personne
		// + +-- id de la première personne
		// +-- id du premier module
		foreach (explode(";",$HTTP_POST_VARS["IDS_ACTION"]) as $s)
		{
			$i = strpos($s,":");
			$iIdMod = substr($s,0,$i);
			$sIdPers = substr($s,($i+1));
			
			$oModule = new CModule($oProjet->oBdd,$iIdMod);
			$oModule->retirerPersonnes(explode(",",$sIdPers),$url_iIdStatut);
		}
	}
	else if ($HTTP_POST_VARS["ACTION"] == "tri")
	{
		$url_iModeTri = (isset($HTTP_POST_VARS["TRI"]) ? $HTTP_POST_VARS["TRI"] : TRI_CROISSANT);
	}
}

// ---------------------
// Initialiser les variables globales
// ---------------------
$sOrdreTri = ($url_iModeTri == TRI_CROISSANT ? "ASC" : "DESC");

// ---------------------
// Initialiser la formation
// ---------------------
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);

// ---------------------
// Rechercher les personnes inscrites d'après le statut
// ---------------------
switch ($url_iIdStatut)
{
	case STATUT_PERS_CONCEPTEUR:
		$oFormation->initConcepteurs($sOrdreTri);
		$aoPersonnes = &$oFormation->aoConcepteurs;
		$sTitreColonne = _("La liste des concepteurs");
		break;
		
	case STATUT_PERS_TUTEUR:
		$oFormation->initTuteurs($sOrdreTri);
		$aoPersonnes = &$oFormation->aoTuteurs;
		$sTitreColonne = _("La liste des tuteurs");
		break;
		
	case STATUT_PERS_ETUDIANT:
		$oFormation->initInscrits($sOrdreTri);
		$aoPersonnes = &$oFormation->aoInscrits;
		$sTitreColonne = _("La liste des &eacute;tudiants");
		break;
		
	default:
		$aoPersonnes = array();
		$sTitreColonne = "&nbsp;";
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ass_multiple-pers.tpl");

$oSet_ImgTriAsc  = $oTpl->defVariable("SET_IMAGE_TRI_ASC");
$oSet_ImgTriDesc = $oTpl->defVariable("SET_IMAGE_TRI_DESC");

$oTpl->remplacer("{formation->id}",$url_iIdForm);
$oTpl->remplacer("{statut->id}",$url_iIdStatut);

$oTpl->remplacer("{colonne->titre}",$sTitreColonne);

$oTpl->remplacer("{tri->image}",($url_iModeTri == TRI_CROISSANT ? $oSet_ImgTriAsc : $oSet_ImgTriDesc));
$oTpl->remplacer("{tri->mode}",($url_iModeTri == TRI_CROISSANT ? TRI_DECROISSANT : TRI_CROISSANT));

$oBloc_Personne = new TPL_Block("BLOCK_PERSONNE",$oTpl);
$oBloc_Personne->beginLoop();

$sStyleColonne = NULL;

$iPosPersonne = 1;

foreach ($aoPersonnes as $oPersonne)
{
	$sStyleColonne = ($sStyleColonne == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
	
	$oBloc_Personne->nextLoop();
	
	$oBloc_Personne->remplacer("{colonne->style}",$sStyleColonne);
	
	$oBloc_Personne->remplacer("{personne->pos}",$iPosPersonne++);
	$oBloc_Personne->remplacer("{personne->id}",$oPersonne->retId());
	$oBloc_Personne->remplacer("{personne->nom}",$oPersonne->retNomComplet(TRUE));
}

$oBloc_Personne->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

