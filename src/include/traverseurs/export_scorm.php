<?php

//define ('TYPE_MIME', 'text/html') ; define('CRLF', "<br />");
//define ('TYPE_MIME', 'text/plain'); define('CRLF', "\n");

//header('Content-Type: '.TYPE_MIME.'; charset=utf-8');

require_once(dirname(__FILE__).'/../../globals.inc.php');
require_once(dirname(__FILE__).'/traverseur_scorm.class.php');

$oProjet = new CProjet();

$oTraverseurScorm = new CTraverseurScorm($oProjet->oBdd);
$oTraverseurScorm->defElementATraverser(92, TYPE_FORMATION); // 92 93 155
//$oTraverseurScorm->defElementATraverser(7702, TYPE_SOUS_ACTIVITE);
$oTraverseurScorm->demarrer();

$oTraverseurScorm->enregistrerPaquetScorm();
$oTraverseurScorm->envoyerPaquetScorm();
//$oTraverseurScorm->effacerPaquetScorm();

?>