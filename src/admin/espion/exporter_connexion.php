<?php

/*
** Fichier ................: exporter_connexion.php
** Description ............: 
** Date de création .......: 26/02/2003
** Dernière modification ..: 09/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");
require_once(dir_database("evenement.tbl.php"));

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_EXPORT_TABLE_EVENEMENT");

// ---------------------
// Exporter le fichier CSV
// ---------------------
$sNomFichierCSV = "even-".$oProjet->oUtilisateur->retPseudo().".csv";

$oEvenement = new CEvenement($oProjet->oBdd);
$sNomAExporter = $oEvenement->initFichierExporter($sNomFichierCSV);

$sFichierExporter = ereg_replace(dir_document_root(),"/",$sNomAExporter);

$oProjet->terminer();
?>
<html>
<head>
<title>Exporter le fichier des connexions</title>
<script type="text/javascript" language="javascript">
<!--

function envoyer() { return document.forms[0].submit(); }

//-->
</script>
</head>
<body onload="envoyer()">
<form action="<?=dir_code_lib('download.php',FALSE,FALSE)?>" method="get" target="_self">
<input type="hidden" name="f" value="<?=$sFichierExporter?>">
</form>
</body>
</html>

