<?php

/*
** Fichier ................: deconnexion.php
** Description ............: Se déconnecter de la plate-forme.
** Date de création .......: 01/06/2001
** Dernière modification ..: 30/06/2004
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
include_once("evenement.php");
$oProjet->effacerInfosSession();
$oProjet->terminer();

header("Location: {$oProjet->sUrlLogin}");
?>
