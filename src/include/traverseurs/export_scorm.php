<?php

require_once(dirname(__FILE__).'/../../globals.inc.php');
require_once(dirname(__FILE__).'/traverseur_scorm.class.php');

$oProjet = new CProjet();

header('Content-Type: text/plain; charset=utf-8');

$oTraverseurScorm = new CTraverseurScorm($oProjet->oBdd);
$oTraverseurScorm->defIdDepart(92, TYPE_FORMATION);
//$oTraverseurScorm->defIdDepart(7702, TYPE_SOUS_ACTIVITE);
$oTraverseurScorm->demarrer();

echo $oTraverseurScorm->retContenuManifest();
$oTraverseurScorm->enregistrerPaquetScorm();

?>