<?php

/*
** Fichier ................: dossier_formations-menu.php
** Description ............: 
** Date de cr�ation .......: 04/04/2005
** Derni�re modification ..: 29/09/2005
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

// {{{ Ins�rer ces lignes dans l'en-t�te de la page html
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

