<?php

/*
** Fichier ................: deposer_fichiers-valider.php
** Description ............: 
** Date de cr�ation .......: 26/01/2005
** Derni�re modification ..: 16/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_code_lib("fichiers_permis.inc.php"));

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sNomFichierCopier = (empty($HTTP_POST_FILES["nomFichierCopier"]["name"]) ? "none" : $HTTP_POST_FILES["nomFichierCopier"]["name"]);
$url_sRepDestination   = (empty($HTTP_POST_VARS["repDest"]) ? NULL : $HTTP_POST_VARS["repDest"]);
$url_bDezippe          = (empty($HTTP_POST_VARS["dezipFichier"]) ? TRUE : FALSE);

// ---------------------
// D�poser un fichier
// ---------------------
if ($url_sNomFichierCopier != "none" && validerFichier($url_sNomFichierCopier))
{
$sBlocPageHtml = <<<BLOC_PAGE_HTML
<html>
<head>
<script type="text/javascript" language="javascript">
<!--
function init()
{
	if (window.opener && window.opener.deposer_fichiers_callback)
		window.opener.deposer_fichiers_callback();
	self.close();
}
//-->
</script>
</head>
<body onload="init()">
</body>
</html>
BLOC_PAGE_HTML;
	
	$url_sNomRepertoireCopie = $url_sRepDestination;
	
	if ("." != $HTTP_POST_VARS["nomRepertoireCopie"])
		$url_sNomRepertoireCopie .= $HTTP_POST_VARS["nomRepertoireCopie"]."/";
	
	$sDestination = dir_document_root($url_sNomRepertoireCopie);
	
	move_uploaded_file($HTTP_POST_FILES["nomFichierCopier"]["tmp_name"],$sDestination.$url_sNomFichierCopier);
	
	@chmod($sDestination.$url_sNomFichierCopier,0644);
	
	// D�compress� le fichier zip
	if ($url_bDezippe)
		unzip($sDestination,$url_sNomFichierCopier);
}
else
{
$sBlocPageHtml = <<<BLOC_PAGE_HTML
<html>
<head>
</head>
<body>
<p>&nbsp;</p>
<p style="text-align: center; font-weight: bold;">Ce type de fichier n'est pas autoris� par la plate-forme</p>
</body>
</html>
BLOC_PAGE_HTML;
}

echo $sBlocPageHtml;

exit();

?>
