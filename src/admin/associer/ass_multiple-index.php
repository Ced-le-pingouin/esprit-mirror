<?php
require_once("globals.inc.php");

// ---------------------
// Récupération des paramètres contenu dans l'url
// ---------------------
$url_iIdForm = (isset($HTTP_GET_VARS["ID_FORM"]) ? $HTTP_GET_VARS["ID_FORM"] : 0);
$url_iIdStatut = (isset($HTTP_GET_VARS["STATUT_PERS"]) ? $HTTP_GET_VARS["STATUT_PERS"] : 0);

// ---------------------
// Template
// ---------------------
$sTitrePrincipal = "Association multiple";

$oTpl = new Template("ass_multiple-index.tpl");
$oTpl->remplacer("{fenetre->titre}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{outil->titre}",rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{formation->id}",$url_iIdForm);
$oTpl->remplacer("{personne->statut}",$url_iIdStatut);
$oTpl->afficher();
?>

