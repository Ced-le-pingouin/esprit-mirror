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
** Fichier ................: exporter-filtres.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 22/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("typestatutpers.tbl.php"));

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_EXPORT_TABLE_PERSONNE");

$oProjet->initFormationsUtilisateur();

$oTpl = new Template("exporter-filtres.tpl");

$oBlocTypeStatutPers = new TPL_Block("BLOCK_STATUT_PERSONNE",$oTpl);

$oTpl->remplacer("{onglet->titre}",htmlentities("Liste des personnes trouvées"));

// Liste de l'alphabet
$sListeAlphabet = NULL;

for ($a = 97; $a <= 122; $a++)
	$sListeAlphabet .= "<a"
		." href=\"javascript: top.oPersonnes().location.hash = '#".chr($a)."'; void(0);\""
		." target=\"personnes\""
		." onfocus=\"blur()\""
		.">".chr($a)."</a>&nbsp;";

$oTpl->remplacer("{liste_alphabet}",$sListeAlphabet);

// {{{ Afficher les statuts de l'utilisateur
$oTypeStatutPers = new CTypeStatutPers($oProjet->oBdd);
$oTypeStatutPers->initTypesStatutsPers();

$oBlocTypeStatutPers->beginLoop();

foreach ($oTypeStatutPers->aoTypesStatutsPers as $oStatutPers)
{
	$iIdTypeStatutPers = $oStatutPers->retId();
	
	if (STATUT_PERS_RESPONSABLE == $iIdTypeStatutPers ||
		STATUT_PERS_CONCEPTEUR == $iIdTypeStatutPers ||
		STATUT_PERS_TUTEUR == $iIdTypeStatutPers ||
		STATUT_PERS_ETUDIANT == $iIdTypeStatutPers)
	{
		$oBlocTypeStatutPers->nextLoop();
		$oBlocTypeStatutPers->remplacer(array("{statut.id}","{statut.nom}"),array($iIdTypeStatutPers,$oStatutPers->retNomStatutMasculin()));
	}
}

$oBlocTypeStatutPers->afficher();
// }}}

// Liste des formations disponibles
$oBloc_formation = new TPL_Block("BLOCK_OPTION_FORMATION",$oTpl);
$oBloc_formation->beginLoop();

if (is_array($oProjet->aoFormations))
{
	foreach ($oProjet->aoFormations as $oFormation)
	{
		$sNomFormation = $oFormation->retNom();
		
		if (strlen($sNomFormation) > 38)
			$sNomFormation = substr($sNomFormation,0,38)
				."...";
		
		$oBloc_formation->nextLoop();
		$oBloc_formation->remplacer("{formation->id}",$oFormation->retId());
		$oBloc_formation->remplacer("{formation->nom}",$sNomFormation);
	}
	
	$oBloc_formation->afficher();
}
else
{
	$oBloc_formation->effacer();
}

$oTpl->afficher();

$oProjet->terminer();
?>

