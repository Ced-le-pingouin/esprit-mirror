<?php

/*
** Fichier ................: tableaudebord.php
** Description ............:
** Date de cr�ation .......: 10/11/2005
** Derni�re modification ..: 10/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Initialiser
// ---------------------
$iModalite = $oProjet->oSousActivCourante->retModalite(TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("tableaudebord.tpl");

// {{{ Consigne
$sDescription = convertBaliseMetaVersHtml($oProjet->oSousActivCourante->retDescr());

$oTpl->remplacer("{consigne}",$sDescription);
// }}}

// {{{ Lien du tableau de bord
$oTpl->remplacer("{tableau_de_bord}",convertLien(MODALITE_PAR_EQUIPE == $iModalite ? "[tableaudebord /e]" : "[tableaudebord /i]"));
$oTpl->remplacer("{tableaudebord.niveau.id}",$oProjet->oRubriqueCourante->retId());
$oTpl->remplacer("{tableaudebord.niveau.type}",TYPE_RUBRIQUE);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

