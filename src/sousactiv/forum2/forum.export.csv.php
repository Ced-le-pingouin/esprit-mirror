<?php

/*
** Fichier ................: forum.export.csv.php
** Description ............:
** Date de cr�ation .......: 11/10/2005
** Derni�re modification ..: 25/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

header("Content-Type: application/csv-tab-delimited-table");
header("Content-disposition: filename=forum_".date("d-m-Y").".csv");

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));
require_once("forum_csv.class.php");

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdForum = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);

// ---------------------
// T�l�charger le r�sultat
// ---------------------
$oForumCSV = new CForumCSV(new CBdd(),$url_iIdForum);
$oForumCSV->exporter();

?>

