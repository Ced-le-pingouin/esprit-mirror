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
** Fichier ................: menu.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 28/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once("globals.icones.php");
require_once(dir_admin("awareness","awareness.inc.php",TRUE));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$iIdForm      = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod       = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);
$iIdRubrique  = (is_object($oProjet->oRubriqueCourante) ? $oProjet->oRubriqueCourante->retId() : 0);
$iIdActiv     = (is_object($oProjet->oActivCourante) ? $oProjet->oActivCourante->retId() : 0);
$iIdSousActiv = (is_object($oProjet->oSousActivCourante) ? $oProjet->oSousActivCourante->retId() : 0);


// Si on voit les formations 'en cours', alors quand on cliquera sur le bouton 'en cours/archives',
// on affichera les formations archiv�es.
// Et inversement.
$sTypeAffichageActuel = isset($_GET['sAffiche']) ? $_GET['sAffiche'] : "en_cours";
switch ($sTypeAffichageActuel) {
	case "Archives":
		$sTypeAffichage = "en_cours";
		break;
	default:
		$sTypeAffichage = "Archives";
		break;
}

// ---------------------
// Initialiser
// ---------------------
$iNbrStatuts   = $oProjet->initStatutsUtilisateur($iIdRubrique > 0);
$bPersInscrite = ($oProjet->retIdUtilisateur() > 0);

// vérifier si on est dans un cours ou dans la description -> si description
// on affichera la liste de tous les inscrits à la formation dans le lien courriel
($iIdMod > 0 ? 
$sParamCourriel = "idStatuts=".STATUT_PERS_RESPONSABLE."x".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT
: $sParamCourriel = "idForm =".$iIdForm);

// ---------------------
// Compter le nombre d'outils
// ---------------------
$iNbrOutils = 0;

$asOutils = array("PERM_OUTIL_CORBEILLE"
	, "PERM_OUTIL_CONSOLE"
	, "PERM_OUTIL_PERMISSION"
	, "PERM_OUTIL_ECONCEPT"
	, "PERM_OUTIL_INSCRIPTION"
	, "PERM_OUTIL_EQUIPE"
	, "PERM_OUTIL_STATUT"
	, "PERM_OUTIL_EXPORT_TABLE_PERSONNE");

foreach ($asOutils as $sOutil)
	if ($oProjet->verifPermission($sOutil))
		$iNbrOutils++;

// ---------------------
// Composer le menu
// ---------------------
$sMenu = ($bPersInscrite
		  ? "<a href=\"javascript: void(0);\" onclick=\"profil(); return false;\" onfocus=\"blur()\">"._("Profil")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($iNbrStatuts > 1
		? "<a href=\"javascript: void(0);\" onclick=\"changer_statut(); return false;\" onfocus=\"blur()\">"._("Statuts")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($bPersInscrite
		? "<a href=\"javascript: void(0);\" onclick=\"liste_connectes(); return false;\" onfocus=\"blur()\">"._("Awareness")."</a>&nbsp;|&nbsp;"
		: NULL)
	/*.($iIdRubrique < 1 && $oProjet->verifPermission("PERM_CLASSER_FORMATIONS")
		? "<a href=\"javascript: void(0);\" onclick=\"changer_dossier(); return false;\" onfocus=\"blur()\">"._("Dossier")."</a>&nbsp;|&nbsp;"
		: NULL)*/
	.(($iIdForm > 0) &&($bPersInscrite)
		? "<a href=\"javascript: void(0);\" onclick=\"connexion(); return false;\" onfocus=\"blur()\">"._("Traces")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($iNbrOutils > 0
		? "<a href=\"javascript: void(0);\" onclick=\"outils(); return false;\" onfocus=\"blur()\">"._("Outils")."</a>&nbsp;|&nbsp;" 
		: NULL)
	.($bPersInscrite
		? "<a href=\"javascript: void(0);\" onclick=\"multilingue(); return false;\" onfocus=\"blur()\">"._("Multilinguisme")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($bPersInscrite && $iIdMod>0 && $iIdRubrique==0
		? "<a href=\"javascript: void(0);\" onclick=\"choix_courriel('?idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."x".STATUT_PERS_RESPONSABLE."&typeCourriel=courriel-cours@cours'); return false;\" onfocus=\"blur()\">"._("Courriel")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($bPersInscrite && $iIdMod>0 && $iIdRubrique>0
		? "<a href=\"javascript: void(0);\" onclick=\"choix_courriel('?idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."&typeCourriel=courriel-cours@cours'); return false;\" onfocus=\"blur()\">"._("Courriel")."</a>&nbsp;|&nbsp;"
		: NULL)
	.($bPersInscrite && ($iIdMod==0)
		? "<a href=\"javascript: void(0);\" onclick=\"choix_courriel('?idForm=".$iIdForm."&typeCourriel=courriel-formation@formation'); return false;\" onfocus=\"blur()\">"._("Courriel")."</a>&nbsp;|&nbsp;"
		: NULL)
	."<a href=\"javascript: void(0);\" onclick=\"recharger('?idForm={$iIdForm}&idMod={$iIdMod}&idUnite={$iIdRubrique}&idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}&sAffiche={$sTypeAffichageActuel}'); return false;\" onfocus=\"blur()\">"._("Rafraîchir")."</a>";

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("menu.tpl",FALSE,TRUE));

// ---------------------
// Menu par défaut
// ---------------------
$oTpl->remplacer("{liste_outils}",$sMenu);
$oTpl->remplacer("{deconnexion}","deconnexion.php");

// ---------------------
// Afficher "Lien affichage des 'archives'/'en cours' et 'recherche'"
// ---------------------
$oRetourMenu = new TPL_Block("BLOCK_SELECTION_ARCHIVES",$oTpl);
$sCssArchive = "class=\"archives_encours\"";
if ($sTypeAffichageActuel == "Archives") 
	$sTexteArchive = "<span>En&nbsp;cours</span><span>/</span><span ".$sCssArchive.">Archiv&eacute;es</span>";
else
	$sTexteArchive = "<span ".$sCssArchive.">En&nbsp;cours</span><span>/</span><span>Archiv&eacute;es</span>";

$sFormationArchives = (($oProjet->verifPermission("PERM_VOIR_SESSION_ARCHIVES"))
		? "<a href=\"javascript: void(0);\" onclick=\"recharger('?idForm={$iIdForm}&idMod={$iIdMod}&idUnite={$iIdRubrique}&idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}&sAffiche={$sTypeAffichage}'); return false;\" onfocus=\"blur()\">".$sTexteArchive."</a>"
		: NULL);
$sRechercheFormation = "<a href=\"javascript: void(0);\" onclick=\"return false;\" onfocus=\"blur()\"><span>Rechercher une formation</span></a>";
					
if ($iIdRubrique == 0 && ($iIdPers = $oProjet->retIdUtilisateur()) > 0)
{
	$oRetourMenu->remplacer("{archives}",$sFormationArchives);
	//$oRetourMenu->remplacer("{recherche}",$sRechercheFormation);
	$oRetourMenu->afficher();
}
else
	$oRetourMenu->effacer();

// ---------------------
// Afficher "Retour menu" ?
// ---------------------
$oRetourMenu = new TPL_Block("BLOCK_RETOUR_MENU",$oTpl);

if ($iIdRubrique > 0)
{
	$oRetourMenu->remplacer("{retour_menu}","zone_menu-index.php?idForm={$iIdForm}&idMod={$iIdMod}&idUnite=0&idActiv=0&idSousActiv=0&sAffiche={$sTypeAffichageActuel}");
	$oRetourMenu->afficher();
}
else
	$oRetourMenu->effacer();

$oTpl->afficher();

$oProjet->terminer();

?>

