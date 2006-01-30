<?php

/*
** Fichier ................: modifier_glossaire.php
** Description ............:
** Date de création .......: 31/07/2004
** Dernière modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

if (isset($HTTP_GET_VARS["glossaire_id"]))
{
	include_once("glossaire_titre.inc.php");
	exit();
}

$url_iIdGlossaire = $HTTP_GET_VARS["idGlossaire"];

$sGlossaireTitre = NULL;

if ($url_iIdGlossaire > 0)
{
	require_once(dir_database("bdd.class.php"));
	$oBdd = new CBdd();
	$oGlossaire = new CGlossaire($oBdd,$url_iIdGlossaire);
	$sGlossaireTitre = htmlentities($oGlossaire->retTitre());
	unset($oBdd);
}

$oTpl = new template("glossaire_titre.tpl");

$oBloc_GlossaireTitre = new TPL_Block("BLOCK_GLOSSAIRE_TITRE",$oTpl);

$oSet_GlossaireTitre = $oTpl->defVariable("SET_GLOSSAIRE_TITRE");

$oBloc_GlossaireTitre->ajouter($oSet_GlossaireTitre);
$oBloc_GlossaireTitre->afficher();

$oTpl->remplacer("{menu}","?menu=1");

$oTpl->remplacer("{glossaire->id}",$url_iIdGlossaire);
$oTpl->remplacer("{glossaire->titre}",$sGlossaireTitre);

$oTpl->afficher();
?>
