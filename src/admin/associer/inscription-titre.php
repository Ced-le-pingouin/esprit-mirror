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

$url_sNomFormation = (isset($_GET["TP"]) ? $_GET["TP"] : "&nbsp;");
$url_sSousTitre = (isset($_GET["ST"]) ? $_GET["ST"] : "");
$url_iNumeroFormation = (isset($_GET["idform"]) ? $_GET["idform"] : "");

$oTpl = new Template(dir_theme("dialog-titre-2.tpl",FALSE,TRUE));

$oBlock_Head = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlock_Head->effacer();

$oTpl->remplacer("{class_style}","dialog_titre_principal_2");
$oTpl->remplacer("{titre_principal}",urldecode($url_sNomFormation));
$oTpl->remplacer("{sous_titre}",urldecode($url_sSousTitre)." <em>(n&deg; formation : ".$url_iNumeroFormation.")</em>");

$oTpl->afficher();

?>
