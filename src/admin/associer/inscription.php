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

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForm = empty($_GET["idform"]) ? 0 : $_GET["idform"];

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_ASS_ETUDIANT_COURS");

// ---------------------
// Initialisation
// ---------------------
$g_iIdStatututilisateur = $oProjet->retStatutUtilisateur();

// Statuts de l'utilisateur
$amStatutsUtilisateur = $oProjet->retListeStatut();
array_unshift($amStatutsUtilisateur, NULL);

if (STATUT_PERS_ADMIN != $g_iIdStatututilisateur)
{
	$amStatutsUtilisateur[STATUT_PERS_ADMIN] = NULL;
	$amStatutsUtilisateur[STATUT_PERS_RESPONSABLE_POTENTIEL] = NULL;
}

if ($url_iIdForm < 1
	|| !$oProjet->verifPermission("PERM_ASS_RESP_SESSION"))
	$amStatutsUtilisateur[STATUT_PERS_RESPONSABLE] = NULL;

$amStatutsUtilisateur[STATUT_PERS_CONCEPTEUR_POTENTIEL] = NULL;

if ($url_iIdForm < 1
	|| !$oProjet->verifPermission("PERM_ASS_CONCEPT_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_CONCEPTEUR] = NULL;

$amStatutsUtilisateur[STATUT_PERS_CHERCHEUR] = NULL;

if ($url_iIdForm < 1
	|| !$oProjet->verifPermission("PERM_ASS_TUTEUR_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_TUTEUR] = NULL;

$amStatutsUtilisateur[STATUT_PERS_COTUTEUR] = NULL;

if ($url_iIdForm < 1
	|| !$oProjet->verifPermission("PERM_ASS_ETUDIANT_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_ETUDIANT] = NULL;

$amStatutsUtilisateur[STATUT_PERS_VISITEUR] = NULL;

// Sélectionner le statut par défaut
$iStatutPersDepart = NULL;

for ($i = STATUT_PERS_DERNIER; $i > STATUT_PERS_PREMIER; $i--)
	if ($amStatutsUtilisateur[$i]["IdStatut"] == $i)
	{
		$iStatutPersDepart = $i;
		break;
	}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("inscription.htm");

$oTpl->remplacer("{Formation.id}", $oProjet->oFormationCourante->retId());
$oTpl->remplacer("{StatutUtilisateurDepart}", $iStatutPersDepart);

// Statut des utilisateurs
$oBlocStatutUtilisateur = new TPL_Block("BLOCK_STATUT_PERSONNE", $oTpl);
$oBlocStatutUtilisateur->beginLoop();

foreach ($amStatutsUtilisateur as $amStatutUtilisateur)
{
	if (empty($amStatutUtilisateur))
		continue;

	$oBlocStatutUtilisateur->nextLoop();
	$oBlocStatutUtilisateur->remplacer(
		array("{StatutPersonne.id}",
			"{StatutPersonne.checked}",
			"{StatutPersonne.nom}"),
		array($amStatutUtilisateur["IdStatut"],
			($iStatutPersDepart == $amStatutUtilisateur["IdStatut"] ? " selected=\"selected\"" : NULL),
			$amStatutUtilisateur["NomStatut"]));
}

$oBlocStatutUtilisateur->afficher();

// Afficher le template
$oTpl->afficher();

?>
