<?php

/*
** Fichier ................: deconnexion.php
** Description ............: Se d�connecter de la plate-forme.
** Date de cr�ation .......: 01/06/2001
** Derni�re modification ..: 30/06/2004
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
