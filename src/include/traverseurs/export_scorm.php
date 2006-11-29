<?php

header('Content-Type: text/plain; charset=utf-8');

//require_once(dirname(__FILE__).'/../../globals.inc.php');
//require_once(dirname(__FILE__).'/traverseur_scorm.class.php');
//
//$oProjet = new CProjet();
//
//$oTraverseurScorm = new CTraverseurScorm($oProjet->oBdd);
//$oTraverseurScorm->defElementATraverser(92, TYPE_FORMATION);
////$oTraverseurScorm->defElementATraverser(7702, TYPE_SOUS_ACTIVITE);
//$oTraverseurScorm->demarrer();
//
//echo $oTraverseurScorm->retContenuManifest();
//$oTraverseurScorm->enregistrerPaquetScorm();


define ('TYPE_MIME', 'text/html') ; define('CRLF', "<br />");
//define ('TYPE_MIME', 'text/plain'); define('CRLF', "\n");

header('Content-Type: '.TYPE_MIME.'; charset=utf-8');

require_once(dirname(__FILE__).'/../../lib/std/IterateurDossier.php');
require_once(dirname(__FILE__).'/../../lib/std/IterateurFiltreDossier.php');

$sDossier = "C:\Program Files";
$itr = new IterateurFiltreDossier(new IterateurDossier($sDossier), '/x/i');
//$itr = new IterateurDossier($sDossier);
for (; $itr->estValide(); $itr->suiv())
{
	$o = $itr->courant();
	echo $o->retChemin().CRLF;
}
echo CRLF.$itr->taille()." éléments dans le dossier $sDossier".CRLF;

?>