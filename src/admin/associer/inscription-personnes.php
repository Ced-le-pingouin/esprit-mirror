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

$url_iIdForm          = empty($_GET["idform"]) ? 0 : $_GET["idform"];
$url_iTypeUtilisateur = empty($_GET["type"]) ? 0 : $_GET["type"];

$oProjet = new CProjet();

$iIdForm = $oProjet->oFormationCourante->retId();

$iNbrPersonnes = $oProjet->initPersonnes($url_iTypeUtilisateur, $iIdForm);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("inscription-personnes.htm");

// Afficher les personnes
$oBlocPersonne       = new TPL_Block("BLOCK_PERSONNE", $oTpl);
$oBlocAucunePersonne = new TPL_Block("BLOCK_ELSE_PERSONNE", $oTpl);

if ($iNbrPersonnes > 0)
{
	$oBlocPersonne->beginLoop();

	foreach ($oProjet->aoPersonnes as $oPersonne)
	{
		$oBlocPersonne->nextLoop();

		$oBlocPersonne->remplacer(
			array("{Personne.Id}",
				"{Personne.Nom}",
				"{Personne.Prenom}",
				"{Personne.Pseudo}",
				"{Personne.class}"),
			array($oPersonne->retId(),
				emb_htmlentities(strtoupper($oPersonne->retNom())),
				$oPersonne->retPrenom(),
				$oPersonne->retPseudo(),
				("F" == $oPersonne->retSexe() ? "girl" : "boy"))
		);
	}

	$oBlocPersonne->afficher();
	$oBlocAucunePersonne->effacer();
}
else
{
	$oBlocPersonne->effacer();
	$oBlocAucunePersonne->afficher();
}

$oTpl->afficher();

?>
