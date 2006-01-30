<?php

/*
** Fichier ................: forum_export.php
** Description ............: 
** Date de création .......: 26/10/2005
** Dernière modification ..: 26/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

if (!$oProjet->verifPermission("PERM_FORUM_EXPORTER_CSV"))
	exit("<html><body></body></html>");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum = (empty($HTTP_GET_VARS["idForum"]) ? NULL : $HTTP_GET_VARS["idForum"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("forum_export.tpl");
$oTpl->remplacer("{forum.id}",$url_iIdForum);
$oTpl->afficher();

$oProjet->terminer();

?>

