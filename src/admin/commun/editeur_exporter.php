<?php

/*
** Fichier ................: editeur-exporter.php
** Description ............:
** Date de cr�ation .......: 29/06/2004
** Derni�re modification ..: 30/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

$url_sTexte            = (empty($HTTP_POST_VARS["edition"]) ? NULL : stripslashes($HTTP_POST_VARS["edition"]));
$url_sNomFichierExport = (empty($HTTP_POST_VARS["f"]) ? "fichier_cfj.txt" : stripslashes($HTTP_POST_VARS["f"]));

$url_sNomFichierExport = str_replace("\"","",$url_sNomFichierExport);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename={$url_sNomFichierExport}");
echo $url_sTexte;
?>
