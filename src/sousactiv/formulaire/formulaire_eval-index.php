<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Sous-activité ..........: formulaire_eval-index.php
** Description ............: 
** Date de création .......: 05/11/2004
** Dernière modification ..: 09/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFCSousActiv = (empty($HTTP_GET_VARS["idFCSousActiv"]) ? 0 : $HTTP_GET_VARS["idFCSousActiv"]);

// ---------------------
// Initialiser
// ---------------------
$sTitreFenetre = "Evaluation";

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

$bPeutEvaluerFormulaire  = $oProjet->oModuleCourant->verifTuteur($iMonIdPers);
$bPeutEvaluerFormulaire &= $oProjet->verifPermission("PERM_EVALUER_FORMULAIRE");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("formulaire_eval-index.tpl");

$oTpl->remplacer("{html->title}",htmlentities($sTitreFenetre));

$oTpl->remplacer("{g_iIdNiveau}",$url_iIdNiveau);
$oTpl->remplacer("{g_iTypeNiveau}",$url_iTypeNiveau);

$oTpl->remplacer("{frame['titre']->src}","formulaire_eval-titre.php?tp=".rawurlencode($sTitreFenetre));
$oTpl->remplacer("{frame['tuteurs']->src}","formulaire_eval-tuteurs.php?idFCSousActiv={$url_iIdFCSousActiv}&evalFC={$bPeutEvaluerFormulaire}");
$oTpl->remplacer("{frame['principale']->src}","formulaire_eval.php?idFCSousActiv={$url_iIdFCSousActiv}&evalFC={$bPeutEvaluerFormulaire}");
$oTpl->remplacer("{frame['menu']->src}","");

$oTpl->afficher();

$oProjet->terminer();

?>

