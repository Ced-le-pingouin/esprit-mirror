<?php

/*
** Fichier ................: mail.php
** Description ............:
** Date de création .......: 14/12/2004
** Dernière modification ..: 14/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oTpl = new Template("mail.tpl");

$oTpl->remplacer("{form}","<form>");
$oTpl->remplacer("{/form}","</form>");

$oTpl->afficher();

?>

