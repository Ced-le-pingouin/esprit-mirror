<?php

/*
** Fichier ................: editeur_importer.php
** Description ............:
** Date de création .......: 30/06/2004
** Dernière modification ..: 01/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

// ---------------------
// Importer le fichier
// ---------------------
if (isset($HTTP_POST_FILES["fichier"]))
{
	if ($HTTP_POST_FILES["fichier"]["type"] == "text/plain")
	{
		$sContenuFichier = NULL;
		
		foreach (file($HTTP_POST_FILES["fichier"]["tmp_name"]) as $sLigne)
			$sContenuFichier .= $sLigne;
		
		echo "<html>\n"
			."<head>\n"
			."<script type=\"text/javascript\" language=\"javascript\">\n"
			."<!--\n"
			."function init() {\n"
			."top.opener.top.remplacer('".rawurlencode($sContenuFichier)."');\n"
			."top.close();"
			."}\n"
			."//-->\n"
			."</script>\n"
			."</head>\n"
			."<body onload=\"init()\">\n"
			."</body>\n"
			."</html>\n";
	}
	else
	{
		echo "<html>\n"
			."<head>\n"
			.inserer_feuille_style(NULL,FALSE)
			."<script type=\"text/javascript\" language=\"javascript\">\n"
			."<!--\n"
			."function init() {\n"
			."top.retour();\n"
			."}\n"
			."//-->\n"
			."</script>\n"
			."</head>\n"
			."<body onload=\"init()\">\n"
			."<div style=\"text-align: center;\"><h1>"
			.htmlentities("Ce type de fichier n'est pas accepté. Seuls les fichiers qui ont une extension txt sont acceptés")
			."</h1></div>"
			."</body>\n"
			."</html>\n";
	}
	
	exit();
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("editeur_importer.tpl");
$oBloc_BarreProgression = new TPL_Block("BLOCK_BARRE_DE_PROGRESSION",$oTpl);

$oTpl_BarreProgression = new Template(dir_theme("barre_de_progression.inc.tpl",FALSE,TRUE));
$oSet_BarreProgression = $oTpl_BarreProgression->defVariable("SET_BARRE_DE_PROGRESSION");

$oBloc_BarreProgression->ajouter($oSet_BarreProgression);
$oBloc_BarreProgression->remplacer("{information}","Un instant svp");
$oBloc_BarreProgression->afficher();

$oTpl->afficher();
?>

