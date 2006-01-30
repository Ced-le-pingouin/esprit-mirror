<?php

/*
** Fichier ................: avertissement.php
** Description ............:
** Date de création .......: 08/07/2005
** Dernière modification ..: 12/07/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sAvertissement        = (empty($HTTP_POST_VARS["avertissement"]) ? NULL : $HTTP_POST_VARS["avertissement"]);
$url_bAppliquerChangements = (empty($HTTP_POST_VARS["f"]) ? FALSE : TRUE);

// ---------------------
// Appliquer les changements
// ---------------------
if ($url_bAppliquerChangements)
{
	$sRequeteSql = "UPDATE Projet SET"
		." AvertissementLogin='".MySQLEscapeString($url_sAvertissement)."'"
		." LIMIT 1";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	
	exit("<html>\n"
		."<head>\n"
		."<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."function init() { top.close(); }\n"
		."//-->\n"
		."</script>\n"
		."</head>\n"
		."<body onload=\"init()\"></body>\n"
		."</html>\n"
	);
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_admin("commun","editeur.inc.tpl",TRUE));
$sSetEditeur = $oTpl->defVariable("SET_EDITEUR");

$oTpl = new Template(dir_admin("commun","editeur.tpl",TRUE));
$sSetVisualiseur = $oTpl->defVariable("SET_VISUALISEUR");

// {{{ Editeur
$oBlocEditeur = new TPL_Block("BLOCK_EDITEUR",$oTpl);
$oBlocEditeur->ajouter($sSetEditeur);
$oBlocEditeur->afficher();
// }}}

// {{{ Visualiseur
$oBlocVisualiseur = new TPL_Block("BLOCK_VISUALISATEUR",$oTpl);
$oBlocVisualiseur->effacer();
// }}}

$oTpl->remplacer("{editeur->nom}","avertissement");

$oTpl->remplacer("icones://",dir_icones());
$oTpl->remplacer("editeur://",dir_admin("commun"));

$oTpl->afficher();

$oProjet->terminer();

?>

