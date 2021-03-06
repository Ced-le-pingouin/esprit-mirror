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
** Fichier ................: forum.export.txt.php
** Description ............:
** Date de création .......: 25/10/2005
** Dernière modification ..: 25/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

header("Content-Type: application/octet-stream");
header("Content-disposition: filename=forum_".date("d-m-Y").".txt");

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));
require_once("forum_txt.class.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);

// ---------------------
// Télécharger le résultat
// ---------------------
$oForumCSV = new CForumTXT(new CBdd(),$url_iIdForum);
$oForumCSV->exporter();

?>

