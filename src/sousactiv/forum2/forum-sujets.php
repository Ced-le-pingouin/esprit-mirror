<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: forum-sujets.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 14/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum    = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);
$url_iIdSujet    = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------
$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// {{{ Permissions
$bPeutGererTousSujets  = $oProjet->verifPermission("PERM_MOD_SUJETS_FORUMS");
$bPeutGererTousSujets |= ($oProjet->verifPermission("PERM_MOD_SUJETS_FORUM") && $oProjet->verifModifierModule());

$bPeutAjtSujet = $oProjet->verifPermission("PERM_AJT_SUJET_FORUM");
$bPeutModSujet = $oProjet->verifPermission("PERM_MOD_SUJET_FORUM");
$bPeutSupSujet = $oProjet->verifPermission("PERM_SUP_SUJET_FORUM");

// si la formation est archiv�e et que l'utilisateur n'a pas les droits de modification
if (($oProjet->oFormationCourante->retStatut()== STATUT_ARCHIVE) &&(!$oProjet->verifPermission("PERM_MOD_SESSION_ARCHIVES")))
{
	$bPeutGererTousSujets = $bPeutAjtSujet = $bPeutModSujet = $bPeutSupSujet = FALSE; 
}
// }}}

$bIdEquipeCorrect = TRUE;

// ---------------------
// Forum
// ---------------------
$oForum = new CForum($oProjet->oBdd,$url_iIdForum);

// Vérifier que cet utilisateur est bien associé à une équipe
if (($iModaliteForum = $oForum->retModalite()) != MODALITE_POUR_TOUS)
{
	if ($url_iIdEquipe < 1)
	{
		$bIdEquipeCorrect = $oProjet->verifEquipe();
		$iNbrEquipes = count($oProjet->aoEquipes);
		
		if ($bIdEquipeCorrect)
			$url_iIdEquipe = $oProjet->oEquipe->retId();
		else if ($iNbrEquipes > 0)
			$url_iIdEquipe = $oProjet->aoEquipes[0]->retId();
	}
	else
	{
		$iNbrEquipes = $oProjet->initEquipes();
	}
}

// Accessible aux visiteurs ?
$bAccessibleVisiteurs = (!$bIdEquipeCorrect && $oForum->retAccessibleVisiteurs());

// Compter le nombre de sujets de la personne
$iNbSujets = $oForum->retNombreSujets(($bPeutGererTousSujets ? NULL : $iMonIdPers));

// Vérifier si ce forum est un forum par équipe
$bForumParEquipe = ($iModaliteForum != MODALITE_POUR_TOUS);

// Cette variable va permettre à la plate-forme d'afficher ou non la liste
// des équipes
$bAfficherListeEquipes = ($bForumParEquipe & ($bPeutGererTousSujets | $iModaliteForum != MODALITE_PAR_EQUIPE | (!$bIdEquipeCorrect & $bAccessibleVisiteurs)));

// Vérifier que cette personne est bien inscrite dans une équipe,
// dans le cas contraire il ne pourra pas ajouter/modifier/supprimer un sujet
if ($bForumParEquipe && !$bIdEquipeCorrect && !$bPeutGererTousSujets)
	$bPeutAjtSujet = $bPeutModSujet = $bPeutSupSujet = FALSE;

// ---------------------
// Template
// ---------------------
$oTpl = new Template("forum-sujets.tpl");

$oBlocMenuSujets = new TPL_Block("BLOCK_MENU_SUJETS",$oTpl);

$sSetListeSujets        = $oTpl->defVariable("SET_LISTE_SUJETS");
$sSetListeSujetsEquipes = $oTpl->defVariable("SET_LISTE_SUJETS_EQUIPES");
$sSetMenuAjouterEquipes = $oTpl->defVariable("SET_NOUVEAU_SUJET_EQUIPES");
$sSetMenuAjouter        = $oTpl->defVariable("SET_NOUVEAU_SUJET");
$sSetMenuModifier       = $oTpl->defVariable("SET_MODIFIER_SUJET");
$sSetMenuSupprimer      = $oTpl->defVariable("SET_SUPPRIMER_SUJET");
$sSetMenuSeparateur     = $oTpl->defVariable("SET_MENU_SEPARATEUR");

// {{{ Menu
$sMenuSujets = NULL;

if ($url_iIdForum > 0)
{
	if ($bPeutGererTousSujets && $bForumParEquipe)
		$sMenuSujets .= $sSetMenuAjouterEquipes;
	
	if ($bPeutGererTousSujets || $bPeutAjtSujet)
		$sMenuSujets .= $sSetMenuAjouter;
	
	if ($iNbSujets > 0 && ($bPeutGererTousSujets || $bPeutModSujet))
		$sMenuSujets .= (isset($sMenuSujets) ? $sSetMenuSeparateur : NULL)
			.$sSetMenuModifier;
	
	if ($iNbSujets > 0 && ($bPeutGererTousSujets || $bPeutSupSujet))
		$sMenuSujets .= (isset($sMenuSujets) ? $sSetMenuSeparateur : NULL)
			.$sSetMenuSupprimer;
}

$oBlocMenuSujets->ajouter($sMenuSujets);
// }}}

// ---------------------
// Template de l'onglet
// ---------------------
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab-simple.tpl",FALSE,TRUE));
$sSetOnglet = $oTplOnglet->defVariable("SET_ONGLET");

$oTpl->remplacer("{liste_sujets}",$sSetOnglet);
$oTpl->remplacer("{onglet->texte}",($bAfficherListeEquipes ? $sSetListeSujetsEquipes : $sSetListeSujets));
$oTpl->remplacer("{onglet->titre}",emb_htmlentities($oForum->retNom()." - Liste des sujets"));
$oTpl->remplacer("{onglet->menu}",NULL);
$oTpl->remplacer("{envoi_courriel}","choix_courriel('?idStatuts=".STATUT_PERS_TUTEUR.(MODALITE_POUR_TOUS == $iModaliteForum ? "x".STATUT_PERS_ETUDIANT : "&idEquipes=tous")."&typeCourriel=courriel-forum@{$url_iIdForum}')");

$oBlocMenuSujets->afficher();

if ($bAfficherListeEquipes)
{
	$oBlocEquipe = new TPL_Block("BLOCK_EQUIPE",$oTpl);
	
	if ($iNbrEquipes > 0)
	{
		$oBlocEquipe->beginLoop();
		
		foreach ($oProjet->aoEquipes as $oEquipe)
		{
			$iIdEquipe = $oEquipe->retId();
			
			$oBlocEquipe->nextLoop();
			$oBlocEquipe->remplacer("{equipe->id}",$iIdEquipe);
			$oBlocEquipe->remplacer("{option->selected}",($url_iIdEquipe == $iIdEquipe ? " selected" : NULL));
			$oBlocEquipe->remplacer("{equipe->nom}",emb_htmlentities($oEquipe->retNom()));
		}
	}
	else
	{
		$oBlocEquipe->remplacer("{equipe->id}",0);
		$oBlocEquipe->remplacer("{option->selected}"," selected");
		$oBlocEquipe->remplacer("{equipe->nom}",emb_htmlentities("Pas d'équipe trouvée"));
	}
	
	$oBlocEquipe->afficher();
}

$oTpl->remplacer("{iframe->src}","sujets.php?idForum={$url_iIdForum}&idSujet={$url_iIdSujet}&idEquipe={$url_iIdEquipe}");

$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{sujet->equipe->id}",($bPeutGererTousSujets || MODALITE_PAR_EQUIPE_COLLABORANTE == $iModaliteForum ? 0 : $url_iIdEquipe));
$oTpl->remplacer("{sujet->id}",$url_iIdSujet);
$oTpl->remplacer("{niveau->id}",$url_iIdNiveau);
$oTpl->remplacer("{niveau->type}",$url_iTypeNiveau);
$oTpl->remplacer("{equipe->id}",$url_iIdEquipe);

$oTpl->afficher();

$oProjet->terminer();

?>

