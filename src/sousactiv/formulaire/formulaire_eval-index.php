<?php

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

