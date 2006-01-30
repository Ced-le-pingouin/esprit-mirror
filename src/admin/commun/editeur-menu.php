<?php

/*
** Fichier ................: editeur-menu.php
** Description ............:
** Date de création .......: 23/06/2004
** Dernière modification ..: 30/06/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array(
		array("Exporter","top.exporter()",1,"text-align: left;")
		, array("Importer","top.importer()",1)
		, array("Valider","top.valider()",2)
		, array("Annuler","top.annuler()",2)
	);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

