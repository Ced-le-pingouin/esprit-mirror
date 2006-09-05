<?php
/*
 * Script d'effacement de formations
 * 
 * NOTE: Ce script a fait planter plusieurs fois mon MySQL 4.1.18 sous Windows XP, avec une erreur de lecture/écriture
 * dans le dossier temporaire de MySQL...
 */

header('content-type: text/plain; charset=utf-8');

require_once('../../../globals.inc.php');

// si DEBUG est défini, l'effacement effectif n'a pas lieu, et les messages d'infos sur les formations à garder 
// ou à effacer sont affichés
define('DEBUG', TRUE);

// !!! ATTENTION !!!
// LES NOMBRES CI-DESSOUS SONT LES *ID DES FORMATIONS A GARDER*, CE QUI SIGNIFIE QUE SI CE SCRIPT EST LANCÉ 
// SANS LE MODE DEBUG, **LES FORMATIONS DONT LES ID NE SE TROUVENT PAS CI-DESSOUS SERONT EFFACEES** !!!
$aiIdsFormationsAGarder = array
(
	5, 20, 40, 41, 42, 44, 48, 55, 68, 81,
	82, 92, 93, 94, 95, 96, 99, 100, 110, 111, 
	112, 113, 116, 118, 119, 120, 121, 122, 123, 124,
	126, 127, 128, 129, 139, 141, 143, 144, 145, 146, 
	147, 148, 149, 150, 151, 152, 153
);

$oProjet = new CProjet();
$oFormation = NULL;

$hResult = $oProjet->oBdd->executerRequete
(
	' SELECT' .
	'   IdForm ' .
	' FROM' .
	'   Formation' .
	' WHERE' .
	'   IdForm NOT IN ('.implode(',', $aiIdsFormationsAGarder).')'
);

$aiIdsFormationsAEffacer = array();
while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
{
	$oFormation = new CFormation($oProjet->oBdd, $oEnreg->IdForm);
	$aiIdsFormationsAEffacer[] = $oFormation->retId();
	
	if (defined('DEBUG'))
		print $oFormation->retId()."\n";
	else
		$oFormation->effacer();
}

// affiche les formations à garder, donc celles définies au début de ce fichier, mais aussi les formations
// qui seront supprimées 
if (defined('DEBUG'))
{
	print "à garder : (".count($aiIdsFormationsAGarder).") - ".implode(', ', $aiIdsFormationsAGarder)."\n";
	print "\n";
	print "à effacer : (".count($aiIdsFormationsAEffacer).") - ".implode(', ', $aiIdsFormationsAEffacer)."\n";
}
?>
