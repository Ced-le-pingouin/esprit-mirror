<?php

/*
** Fichier ................: changer_dossier-menu.php
** Description ............: 
** Date de création .......: 02/06/2005
** Dernière modification ..: 29/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));
require_once(dir_locale("dossiers.lang"));

$oProjet = new CProjet();
$oProjet->initPermisUtilisateur(FALSE);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Initialiser
// ---------------------

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Composer le menu
if ($oProjet->verifPermission("PERM_CLASSER_FORMATIONS"))
{
	$aMenus   = array();
	$aMenus[] = array(BTN_CREER_MODIFIER_DOSSIER,"composer_dossiers_formations()",1,"text-align: left;");
	$aMenus[] = array(BTN_VALIDER,"top.oPrincipale().valider()",2);
}
// }}}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

$oProjet->terminer();

?>

