<?php

/*
** Fichier ................: connexion.php
** Description ............: 
** Date de création .......: 24/02/2003
** Dernière modification ..: 26/10/2005
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
$url_iIdPers = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);

// ---------------------
// Initialisation
// ---------------------
if ($oProjet->verifPermission("PERM_OUTIL_EXPORT_TABLE_EVENEMENT"))
	$sPeutExporter = "?exporter=1";
else
	$sPeutExporter = NULL;

// ---------------------
// Frame du Titre
// ---------------------
$sTitrePrincipal = "Trace";

$sSousTitre = (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante)
		? $oProjet->oFormationCourante->retNom()
		: NULL);

$sFrameSrcTitre = NULL;

// ---------------------
// Frame principal
// ---------------------

// Javascript
$sBlockHead = <<<HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principal"]; }
function oExporter() { return top.frames["frameExporter"]; }
function recharger() { top.location = top.location; }
function exporter() { oExporter().location = "exporter_connexion.php"; }
//-->
</script>
HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = "<frameset cols=\"1,*\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n"
	."<frame src=\"".retPageVide()."\" name=\"frameExporter\" scrolling=\"no\">\n"
	."<frame src=\"connexion.php?idPers={$url_iIdPers}\" name=\"Principal\" frameborder=\"0\" marginwidth=\"2\" marginheight=\"2\" scrolling=\"yes\">\n"
	."</frameset>\n";

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "connexion-menu.php{$sPeutExporter}";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

