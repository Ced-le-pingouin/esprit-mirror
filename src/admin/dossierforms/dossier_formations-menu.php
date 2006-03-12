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
** Fichier ................: dossier_formations-menu.php
** Description ............: 
** Date de création .......: 04/04/2005
** Dernière modification ..: 29/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Initialiser
// ---------------------

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;
// }}}

// {{{ Composer le menu
$aMenus = array();
$aMenus[] = array(BTN_CREER,"top.oListe().ajouter()",1,"text-align: left;");
$aMenus[] = array(BTN_MODIFIER,"top.oListe().modifier()",1);
$aMenus[] = array(BTN_SUPPRIMER,"top.oListe().supprimer()",1);
$aMenus[] = array(BTN_ENREGISTRER_MODIFICATIONS,"top.oPrincipale().sauver()",2);
$aMenus[] = array(BTN_FERMER,"top.fermer()",2);
// }}}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

