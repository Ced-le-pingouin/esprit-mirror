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
$url_sTitrePrincipal = (empty($_GET["tp"]) ? NULL : stripslashes($_GET["tp"]));
$sBlockHead = NULL;
$aMenus   = array();
$aMenus[] = array("Changer de formation","top.choix_formation('{$url_sTitrePrincipal}')",1,"text-align: left;");
$aMenus[] = array("Exportation SCORM","self.location.href='../../include/traverseurs/export_scorm.php?idForm='+top.retIdForm()",2,"text-align: left;");
$aMenus[] = array("Rafraîchir","top.rafraichir()",3,"text-align: center;");
$aMenus[] = array("Fermer","top.close()",4);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>