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
** Fichier ................: info_bulle-index.php
** Description ............:
** Date de création .......: 10/06/2004
** Dernière modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

$sParamsUrl = "?type=".$HTTP_GET_VARS["type"]
	."&idType=".$HTTP_GET_VARS["idType"];

$oTpl = new template(dir_theme("dialogue/dialog_simple-index.tpl",FALSE,TRUE));
$oTpl->remplacer("{html->titre}",htmlentities("Info bulle"));
$oTpl->remplacer("{frame['principale']->src}","info_bulle.php{$sParamsUrl}");
$oTpl->remplacer("{frame['menu']->src}","");
$oTpl->afficher();
?>
