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
** Fichier ................: infos-menu.php
** Description ............: 
** Date de création .......: 10/05/2005
** Dernière modification ..: 10/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// ---------------------
$iNbAdmins = $oProjet->initAdministrateurs();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("infos.tpl");

$oTpl->remplacer("{plateforme.nom}",htmlentities($oProjet->retNom()));
$oTpl->remplacer("{plateforme.courriel}",htmlentities($oProjet->retEmail()));
$oTpl->remplacer("{chat.port}",$oProjet->retNumPortChat());
$oTpl->remplacer("{awareness.port}",$oProjet->retNumPortAwareness());

// {{{ Liste des administrateurs
$oBlocAdmin = new Tpl_Block("BLOCK_ADMINISTRATEUR",$oTpl);

if ($iNbAdmins > 0)
{
	$oBlocAdmin->beginLoop();
	
	foreach ($oProjet->aoAdmins as $oAdmin)
	{
		$oBlocAdmin->nextLoop();
		$oBlocAdmin->remplacer("{personne.nom}",$oAdmin->retNom());
		$oBlocAdmin->remplacer("{personne.prenom}",$oAdmin->retPrenom());
		$oBlocAdmin->remplacer("{personne.courriel}",$oAdmin->retEmail());
	}
	
	$oBlocAdmin->afficher();
}
else
	$oBlocAdmin->effacer();
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

