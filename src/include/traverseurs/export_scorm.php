<?php

//define ('TYPE_MIME', 'text/html') ; define('CRLF', "<br />");
//define ('TYPE_MIME', 'text/plain'); define('CRLF', "\n");

//header('Content-Type: '.TYPE_MIME.'; charset=utf-8');

require_once(dirname(__FILE__).'/../../globals.inc.php');
require_once(dirname(__FILE__).'/traverseur_scorm.class.php');

$iIdForm = isset($_GET['idForm'])?$_GET['idForm']:93; // le 93 est temporaire, à des fins de test  // 92 93 154 155

$oProjet = new CProjet();

$oTraverseurScorm = new CTraverseurScorm($oProjet->oBdd);
$oTraverseurScorm->defElementATraverser($iIdForm, TYPE_FORMATION);
//$oTraverseurScorm->defElementATraverser(7702, TYPE_SOUS_ACTIVITE);
$oTraverseurScorm->demarrer();

$oTraverseurScorm->enregistrerPaquetScorm();
$oTraverseurScorm->envoyerPaquetScorm();
//$oTraverseurScorm->effacerPaquetScorm();

$oProjet->terminer();

?>