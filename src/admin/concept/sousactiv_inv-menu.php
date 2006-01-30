<?php

/*
** Fichier ................: sousactiv_inv-menu.php
** Description ............: 
** Date de création .......: 16/11/2005
** Dernière modification ..: 16/11/2005
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
$aMenus[] = array(BTN_ENREGISTRER_MODIFICATIONS,"top.oPrincipale().envoyer()",1);
$aMenus[] = array(BTN_FERMER,"top.close()",1);
// }}}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

