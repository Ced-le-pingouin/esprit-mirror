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

$url_iIdForm              = empty($_GET["idform"]) ? 0 : $_GET["idform"];
$url_iIdStatutUtilisateur = empty($_GET["idstatut"]) ? 0 : $_GET["idstatut"];

if ($url_iIdStatutUtilisateur < 1)
	exit;

$oProjet = new CProjet();

$oTpl = new Template("inscription-inscrits.htm");

$oBlocPersonnes      = new TPL_Block("BLOCK_PERSONNES", $oTpl);
$oBlocPersonne       = new TPL_Block("BLOCK_PERSONNE", $oBlocPersonnes);
$oBlocAucunePersonne = new TPL_Block("BLOCK_ELSE_PERSONNES", $oTpl);

$oBlocModule      = new TPL_Block("BLOCK_MODULE", $oTpl);
$oBlocAucunModule = new TPL_Block("BLOCK_ELSE_MODULE", $oTpl);

if (STATUT_PERS_ETUDIANT == $url_iIdStatutUtilisateur
	|| STATUT_PERS_TUTEUR == $url_iIdStatutUtilisateur
	|| STATUT_PERS_CONCEPTEUR == $url_iIdStatutUtilisateur)
{
	$oFormation = new CFormation($oProjet->oBdd, $url_iIdForm);
	$oFormation->initModules();

	$oBlocModule->beginLoop();

	foreach ($oFormation->aoModules as $oModule)
	{
		$iIdMod = $oModule->retId();
		$iNbPersonnes = $oModule->initPersonnes($url_iIdStatutUtilisateur);
		
		$oBlocModule->nextLoop();		
		$oBlocModule->remplacer("{Personne.nombre}", $iNbPersonnes);
		$oBlocModule->remplacer(
			array("{Module.Id}", "{Module.Nom}")
			, array($iIdMod, emb_htmlentities($oModule->retNom())));
		
		$oBlocModulePers = new TPL_Block("BLOCK_PERSONNE", $oBlocModule);
				
		if ($iNbPersonnes > 0)
		{
					
			$oBlocModulePers->beginLoop();

			foreach ($oModule->aoPersonnes as $oPersonne)
			{
				$oBlocModulePers->nextLoop();
				$oBlocModulePers->remplacer("{Module.Id}", $iIdMod);
				$oBlocModulePers->remplacer(
					array("{Personne.Id}", "{Personne.Nom}", "{Personne.Prenom}")
					, array($oPersonne->retId(), emb_htmlentities(strtoupper($oPersonne->retNom())), emb_htmlentities($oPersonne->retPrenom())));
			}
			
			$oBlocModulePers->afficher();
		}
		else
			$oBlocModulePers->effacer();
	}

	$oBlocModule->afficher();

	$oBlocPersonnes->effacer();
	$oBlocAucunePersonne->effacer();
	$oBlocAucunModule->effacer();
}
else
{
	$oBlocModule->effacer();
	$oBlocAucunModule->effacer();
	
	if ($oProjet->initPersonnes($url_iIdStatutUtilisateur, $url_iIdForm) > 0)
	{
		$oBlocPersonne->beginLoop();

		foreach ($oProjet->aoPersonnes as $oPersonne)
		{
			$oBlocPersonne->nextLoop();
			$oBlocPersonne->remplacer(
				array("{Personne.Nom}","{Personne.Prenom}"),
				array(emb_htmlentities(strtoupper($oPersonne->retNom())), emb_htmlentities($oPersonne->retPrenom())));
		}

		$oBlocPersonne->afficher();
		$oBlocPersonnes->afficher();
		$oBlocAucunePersonne->effacer();
	}
	else
	{
		$oBlocPersonnes->effacer();
		$oBlocAucunePersonne->afficher();
	}
}

$oTpl->afficher();

?>
