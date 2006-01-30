<?php

/*
** Fichier ................: composer_galerie.php
** Description ............: 
** Date de création .......: 12/09/2005
** Dernière modification ..: 06/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

error_reporting(E_ALL);

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));
require_once(dir_locale("galerie.lang"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

if (!$oProjet->verifPermission("PERM_COMPOSER_GALERIE"))
	exit("Vous n'&ecirc;tes pas autoris&eacute; &agrave; utiliser l'outil 'Composer sa galerie'");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSousActiv    = (empty($HTTP_POST_VARS["idSA"]) ? (empty($HTTP_GET_VARS["idSA"]) ? 0 : $HTTP_GET_VARS["idSA"]) : $HTTP_POST_VARS["idSA"]);
$url_sAction         = (empty($HTTP_POST_VARS["action"]) ? NULL : $HTTP_POST_VARS["action"]);
$url_iIdPers         = (empty($HTTP_POST_VARS["personne"]) ? 0 : $HTTP_POST_VARS["personne"]);
$url_iDocument       = (empty($HTTP_POST_VARS["document"]) ? 0 : $HTTP_POST_VARS["document"]);
$url_iIdCollecticiel = (empty($HTTP_POST_VARS["collecticiel"]) ? 0 : $HTTP_POST_VARS["collecticiel"]);

if ($url_iIdSousActiv == 0 || $url_iIdSousActiv != $oProjet->oSousActivCourante->retId())
	exit("Erreur : Identifiant action non correspondant");

// ---------------------
// Initialiser
// ---------------------
$oGalerie = new CGalerie($oProjet->oBdd,$oProjet->oSousActivCourante->retId());

// {{{ Appliquer les changements
if (isset($url_sAction))
{
	$oGalerie->effacerRessources(explode(",",(empty($HTTP_POST_VARS["idsres"]) ? "" : $HTTP_POST_VARS["idsres"])));
	$oGalerie->ajouterRessources((empty($HTTP_POST_VARS["ressources"]) ? array() : $HTTP_POST_VARS["ressources"]),FALSE);
}
// }}}

$g_sIdsRes = NULL;

$iNbCollecticiels = $oGalerie->initCollecticiels(TRUE,TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("composer_galerie.tpl");

$asSetTpl = array(
	"collecticiel" => $oTpl->defVariable("SET_COLLECTICIEL")
	, "ressource" => $oTpl->defVariable("SET_RESSOURCE")
);

// {{{ Liste des ressources
$oBlocRessource = new TPL_Block("BLOCK_RESSOURCE",$oTpl);

if ($iNbCollecticiels > 0)
{
	$asRechTpl = array(
		"{ressource.checked}"
		, "{ressource.id}"
		, "{ressource.nom}"
		, "{ressource.etat}"
		, "{ressource.auteur}"
	);
	
	$oBlocRessource->beginLoop();
	
	foreach ($oGalerie->aoCollecticiels as $oCollecticiel)
	{
		if ($url_iIdCollecticiel > 0 && $url_iIdCollecticiel != $oCollecticiel->retId())
			continue;
		
		$oBlocRessource->nextLoop();
		$oBlocRessource->ajouter($asSetTpl["collecticiel"]);
		$oBlocRessource->remplacer("{collecticiel.nom}",htmlentities($oCollecticiel->retNom()));
		
		foreach ($oCollecticiel->aoRessources as $oRessource)
		{
			if (STATUT_RES_ACCEPTEE != ($iStatut = $oRessource->retStatut())
				&& STATUT_RES_APPROF != $iStatut
				&& STATUT_RES_TRANSFERE != $iStatut)
				continue;
			
			if ($url_iDocument > 0 && $url_iDocument != $oRessource->retStatut())
				continue;
			
			$oRessource->initExpediteur();
			
			if ($url_iIdPers > 0 && $url_iIdPers != $oRessource->oExpediteur->retId())
				continue;
			
			$iIdRes = $oRessource->retId();
			
			if ($oRessource->estSelectionne)
				$g_sIdsRes .= (isset($g_sIdsRes) ? "," : NULL)
					.$iIdRes;
			
			$amReplTpl = array(
				($oRessource->estSelectionne ? " checked=\"checked\"" : NULL)
				, $iIdRes
				, htmlentities($oRessource->retNom())
				, htmlentities($oRessource->retTexteStatut())
				, htmlentities($oRessource->oExpediteur->retNom()." ".$oRessource->oExpediteur->retPrenom())
			);
			
			$oBlocRessource->nextLoop();
			$oBlocRessource->ajouter($asSetTpl["ressource"]);
			$oBlocRessource->remplacer($asRechTpl,$amReplTpl);
		}
	}
	
	$oBlocRessource->afficher();
}
// }}}

// {{{ Formulaire
$asRechTpl = array("{personne.value}","{document.value}","{collecticiel.value}","{idsres.value}","{sousactiv.id}");
$amReplTpl = array($url_iIdPers,$url_iDocument,$url_iIdCollecticiel,$g_sIdsRes,$url_iIdSousActiv);
$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

// {{{ Globales
$oTpl->remplacer("{sousactiv.nom}",htmlentities($oProjet->oSousActivCourante->retNom()));
// }}}

// {{{ Traduction des termes
$asRechTpl = array(
	"[TXT_COMPOSER_SA_GALERIE_TITRE]"
	, "[TXT_GALERIE_TITRE]"
	, "[TXT_COMPOSER_SA_GALERIE_CONSIGNE]"
	, "[TXT_TITRE]"
	, "[TXT_ETAT]"
	, "[TXT_DEPOSE_PAR]"
);

$asReplTpl = array(
	htmlentities(TXT_COMPOSER_SA_GALERIE_TITRE)
	, htmlentities(TXT_GALERIE_TITRE)
	, nl2br(htmlentities(TXT_COMPOSER_SA_GALERIE_CONSIGNE))
	, htmlentities(TXT_TITRE)
	, htmlentities(TXT_ETAT)
	, htmlentities(TXT_DEPOSE_PAR)
);

$oTpl->remplacer($asRechTpl,$asReplTpl);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

