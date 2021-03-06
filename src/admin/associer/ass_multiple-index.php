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

// ---------------------
// Récupération des paramètres contenu dans l'url
// ---------------------
$url_iIdForm = (isset($_GET["ID_FORM"]) ? $_GET["ID_FORM"] : 0);
$url_iIdStatut = (isset($_GET["STATUT_PERS"]) ? $_GET["STATUT_PERS"] : 0);

// ---------------------
// Template
// ---------------------
$sTitrePrincipal = _("Association multiple");

$oTpl = new Template("ass_multiple-index.tpl");
$oTpl->remplacer("{fenetre->titre}",emb_htmlentities($sTitrePrincipal));
$oTpl->remplacer("{outil->titre}",phpString2js($sTitrePrincipal));
$oTpl->remplacer("{formation->id}",$url_iIdForm);
$oTpl->remplacer("{personne->statut}",$url_iIdStatut);
$oTpl->afficher();
?>

