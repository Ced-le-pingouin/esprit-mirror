<?php

/*
** Sous-activité ..........: forum-index.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 10/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
** Exemples ...............: <a href="/sousactiv/forum/forum-index.php?idForum={idForum}">Forum de la rubrique 1</a>
**                           <a href="/sousactiv/forum/forum-index.php?idNiveau={ID_RUBRIQUE}&typeNiveau={TYPE_RUBRIQUE}">Forum de la rubrique 1</a>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum    = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_iIdPers     = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
$url_iIdEquipe   = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialiser le forum
// ---------------------
$sTitrePrincipal = "Forum";

$oForum = new CForum(new CBdd(),$url_iIdForum);

if ($url_iIdForum < 1)
{
	$oForum->initForumParType($url_iTypeNiveau,$url_iIdNiveau);
	$url_iIdForum = $oForum->retId();
}

$sParamsUrlTitre = "?tp=".rawurlencode($sTitrePrincipal)
	."&st=".rawurlencode($oForum->retTexteModalite());

$sParamsUrl = "?idForum={$url_iIdForum}"
	."&idNiveau={$url_iIdNiveau}"
	."&typeNiveau={$url_iTypeNiveau}"
	.($url_iIdPers > 0 ? "&idPers={$url_iIdPers}" : NULL)
	.($url_iIdEquipe > 0 ? "&idEquipe={$url_iIdEquipe}" : NULL);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("forum-index.tpl");

$oTpl->remplacer("{html.title}",htmlentities($sTitrePrincipal));

$oTpl->remplacer("{forum.id}",$url_iIdForum);

$oTpl->remplacer("{g_iIdNiveau}",$url_iIdNiveau);
$oTpl->remplacer("{g_iTypeNiveau}",$url_iTypeNiveau);

$oTpl->remplacer("{frame['titre'].src}","forum-titre.php{$sParamsUrlTitre}");
$oTpl->remplacer("{frame['sujets'].src}","forum-sujets.php{$sParamsUrl}");
$oTpl->remplacer("{frame['menu'].src}","forum-menu.php?idForum={$url_iIdForum}");

$oTpl->afficher();

?>

