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

if (isset($_GET['idobj']))
{
	$v_iIdObjForm = $_GET['idobj'];
	$v_iIdFormulaire = $_GET['idformulaire'];
	$v_iNouvPos = $_GET['ordreobj'];
}
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
	$v_iNouvPos = 0;
}
$oTpl = new Template("position_objet.tpl");
$oBlockPos = new TPL_Block("BLOCK_POSITION",$oTpl);
$oBlockFermer = new TPL_Block("BLOCK_FERMER",$oTpl);
if (isset($_GET['deplacer']))
{
	$oBlockPos->effacer();
	$oBlockFermer->afficher();
	$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
	$oObjetFormulaire->retId();
	$oObjetFormulaire->DeplacerObjet($v_iNouvPos);
}
else
{
	$oBlockFermer->effacer();
	$oObjetFormulaire = $oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
	$iOrdreObjFormDepart = $oObjetFormulaire->retOrdreObjForm();
	$aoListeObjFormul = $oObjetFormulaire->retListeObjFormulaire($v_iIdFormulaire);
	if(!empty($aoListeObjFormul))
	{
		$oBlockPos->beginLoop();
		foreach($aoListeObjFormul AS $oObjetFormulaire)
		{
			$oBlockPos->nextLoop();
			$iOrdreObjForm = $oObjetFormulaire->retOrdreObjForm();
			$oBlockPos->remplacer("{ordre_obj_form}",$iOrdreObjForm);
			if ($iOrdreObjForm == $iOrdreObjFormDepart)
				$oBlockPos->remplacer("{obj_actuel}","selected=\"selected\"");
			else
				$oBlockPos->remplacer("{obj_actuel}","");
		}
		$oBlockPos->afficher();
	}
	else
	{
		$oBlockPos->effacer();
	}
}
$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
$oTpl->remplacer("{id_obj}",$v_iIdObjForm);
$oTpl->afficher();	  
$oProjet->terminer();
?>
