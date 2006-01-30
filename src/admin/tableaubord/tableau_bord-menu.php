<?php

/*
** Fichier ................: tableau_bord-menu.php
** Description ............: 
** Date de cr�ation .......: 23/06/2005
** Derni�re modification ..: 28/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Initialiser
// ---------------------

// Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();
$aMenus[] = array("<span id=\"id_title\" style=\"font-size: 7pt;\"></span>",NULL,1,"text-align: left; width: 99%;",FALSE);
$aMenus[] = array(BTN_RAFRAICHIR,"top.rafraichir()",2,"text-align: right;");
$aMenus[] = array("&nbsp;&nbsp;&nbsp;",NULL,3,NULL,FALSE);
$aMenus[] = array(BTN_FERMER,"top.close()",4);

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

