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

$oTpl = new Template("exporter-dialog.tpl");

$oTpl->remplacer("{LISTE_IDPERS->value}",$_GET["LISTE_IDPERS"]);

$oBloc_onglet_champs        = new TPL_Block("BLOCK_ONGLET_CHAMPS",$oTpl);
$oBloc_onglet_type_fichiers = new TPL_Block("BLOCK_ONGLET_TYPE_FICHIERS",$oTpl);

$oTpl_onglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_onglet_champs = $oTpl_onglet->defVariable("SET_ONGLET");

// Onglet "champs"
$oSet_champs = $oTpl->defVariable("SET_CHAMPS_EXPORTER");
$oBloc_onglet_champs->ajouter($oSet_onglet_champs);
$oBloc_onglet_champs->remplacer("{onglet->titre}",str_replace(" ","&nbsp;",mb_convert_encoding("Liste des champs","HTML-ENTITIES","UTF-8")));
$oBloc_onglet_champs->remplacer("{onglet->texte}",$oSet_champs);

// Onglet "types de fichier"
$oSet_type_fichier = $oTpl->defVariable("SET_TYPE_FICHIER");
$oBloc_onglet_type_fichiers->ajouter($oSet_onglet_champs);
$oBloc_onglet_type_fichiers->remplacer("{onglet->titre}",str_replace(" ","&nbsp;",mb_convert_encoding("Types de fichier","HTML-ENTITIES","UTF-8")));
$oBloc_onglet_type_fichiers->remplacer("{onglet->texte}",$oSet_type_fichier);

$oBloc_onglet_champs->afficher();
$oBloc_onglet_type_fichiers->afficher();

$oTpl->afficher();

?>

