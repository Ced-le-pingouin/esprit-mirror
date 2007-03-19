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

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_ASS_ETUDIANT_COURS");

// ---------------------
// Initialisation
// ---------------------
$g_iIdStatututilisateur = $oProjet->retStatutUtilisateur();

// {{{ Statuts de l'utilisateur
$amStatutsUtilisateur = $oProjet->retListeStatut();

if (STATUT_PERS_ADMIN != $g_iIdStatututilisateur)
{
	$amStatutsUtilisateur[STATUT_PERS_ADMIN-1] = NULL;
	$amStatutsUtilisateur[STATUT_PERS_RESPONSABLE_POTENTIEL-1] = NULL;
}

if (!$oProjet->verifPermission("PERM_ASS_RESP_SESSION"))
	$amStatutsUtilisateur[STATUT_PERS_RESPONSABLE-1] = NULL;

$amStatutsUtilisateur[STATUT_PERS_CONCEPTEUR_POTENTIEL-1] = NULL;

if (!$oProjet->verifPermission("PERM_ASS_CONCEPT_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_CONCEPTEUR-1] = NULL;

$amStatutsUtilisateur[STATUT_PERS_CHERCHEUR-1] = NULL;

if (!$oProjet->verifPermission("PERM_ASS_TUTEUR_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_TUTEUR-1] = NULL;

$amStatutsUtilisateur[STATUT_PERS_COTUTEUR-1] = NULL;

if (!$oProjet->verifPermission("PERM_ASS_ETUDIANT_COURS"))
	$amStatutsUtilisateur[STATUT_PERS_ETUDIANT-1] = NULL;

$amStatutsUtilisateur[STATUT_PERS_VISITEUR-1] = NULL;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("inscription.htm");

$oTpl->remplacer("{Formation.id}", $oProjet->oFormationCourante->retId());
$oTpl->remplacer("{StatutUtilisateurDepart}", STATUT_PERS_ETUDIANT);

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
			(STATUT_PERS_ETUDIANT == $amStatutUtilisateur["IdStatut"] ? " selected=\"selected\"" : NULL),
			$amStatutUtilisateur["NomStatut"]));
}

$oBlocStatutUtilisateur->afficher();

// Afficher le template
$oTpl->afficher();

?>
