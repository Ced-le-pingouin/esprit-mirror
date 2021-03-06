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
** Fichier ................: forum_export.php
** Description ............: 
** Date de création .......: 26/10/2005
** Dernière modification ..: 26/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

if (!$oProjet->verifPermission("PERM_FORUM_EXPORTER_CSV"))
	exit("<html><body class=\"dialogue\"></body></html>");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum = (empty($_GET["idForum"]) ? NULL : $_GET["idForum"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("forum_export.tpl");
$oTpl->remplacer("{forum.id}",$url_iIdForum);
$oTpl->afficher();

$oProjet->terminer();

?>

