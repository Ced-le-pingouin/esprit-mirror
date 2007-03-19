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

$url_iIdForm              = empty($_GET["form"]) ? 0 : $_GET["form"];
$url_iIdStatutUtilisateur = empty($_GET["statut"]) ? 0 : $_GET["statut"];

$oProjet = new CProjet();
$iNbPersonnes = $oProjet->initPersonnes($url_iIdStatutUtilisateur, $url_iIdForm);

$oTpl = new Template("inscription-inscrits.htm");

$oBlocInscrit = new TPL_Block("BLOCK_INSCRIT", $oTpl);
$oBlocAucunInscrit = new TPL_Block("BLOCK_ELSE_INSCRIT", $oTpl);

if ($iNbPersonnes > 0)
{
	$oBlocInscrit->beginLoop();
	
	foreach ($oProjet->aoPersonnes as $oInscrit)
	{
		$oBlocInscrit->nextLoop();
		
		$oBlocInscrit->remplacer(
			array("{Inscrit.id}", "{Inscrit.nom}", "{Inscrit.prenom}"),
			array($oInscrit->retId(), $oInscrit->retNom(), $oInscrit->retPrenom()));
	}
	
	$oBlocInscrit->afficher();
	$oBlocAucunInscrit->effacer();
}
else
{
	$oBlocInscrit->effacer();
	$oBlocAucunInscrit->afficher();
}

$oTpl->afficher();

?>
