<?php

/*
** Fichier ................: info_bulle-index.php
** Description ............:
** Date de création .......: 10/06/2004
** Dernière modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

$sParamsUrl = "?type=".$HTTP_GET_VARS["type"]
	."&idType=".$HTTP_GET_VARS["idType"];

$oTpl = new template(dir_theme("dialogue/dialog_simple-index.tpl",FALSE,TRUE));
$oTpl->remplacer("{html->titre}",htmlentities("Info bulle"));
$oTpl->remplacer("{frame['principale']->src}","info_bulle.php{$sParamsUrl}");
$oTpl->remplacer("{frame['menu']->src}","");
$oTpl->afficher();
?>
