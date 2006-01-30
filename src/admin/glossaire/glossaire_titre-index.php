<?php

/*
** Fichier ................: glossaire_titre-index.php
** Description ............:
** Date de cr�ation .......: 30/07/2004
** Derni�re modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

$sParamsUrl = "?idGlossaire=".$HTTP_GET_VARS["idGlossaire"];

$oTpl = new template(dir_theme("dialogue/dialog_simple-index.tpl",FALSE,TRUE));
$oTpl->remplacer("{html->titre}",htmlentities("Modifier le titre du glossaire"));
$oTpl->remplacer("{frame['principale']->src}","glossaire_titre.php{$sParamsUrl}");
$oTpl->remplacer("{frame['menu']->src}","");
$oTpl->afficher();
?>
