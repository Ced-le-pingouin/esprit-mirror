<?php

/*
** Fichier ................: form.init.php
** Description ............:
** Date de cr�ation .......: 31/03/2005
** Derni�re modification ..: 31/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

$oProjet->asInfosSession[SESSION_FORM]      = $g_iFormation;
$oProjet->asInfosSession[SESSION_MOD]       = $g_iModule;
$oProjet->asInfosSession[SESSION_UNITE]     = $g_iRubrique;
$oProjet->asInfosSession[SESSION_ACTIV]     = $g_iActiv;
$oProjet->asInfosSession[SESSION_SOUSACTIV] = $g_iSousActiv;
$oProjet->initSousActivCourante();

$oProjet->initStatutsUtilisateur();

?>

