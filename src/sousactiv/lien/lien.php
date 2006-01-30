<?php

/*
** Fichier ................: lien.php
** Description ............: 
** Date de cr�ation .......: 22/04/2002
** Derni�re modification ..: 21/11/2005
** Auteurs ................: Filippo Porco <filippo.porco@umh.ac.be>
** 
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdSousActiv = (empty($HTTP_GET_VARS["idSousActiv"]) ? 0 : $HTTP_GET_VARS["idSousActiv"]);

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

// ---------------------
// Description
// ---------------------
$sHtmlDescr = NULL;
$sDescription = $oSousActiv->retDescr();

if (strlen($sDescription) > 0)
{
	$sHtmlDescr = "<tr>"
	."<td class=\"cellule_description\">"
	.convertBaliseMetaVersHtml($sDescription)
	."</td>"
	."</tr>\n";
	
	$sHtmlDescr = str_replace(
		array("racine://","{tableaudebord.niveau.id}","{tableaudebord.niveau.type}"),
		array(dir_root_plateform(NULL,FALSE),$oProjet->oRubriqueCourante->retId(),TYPE_RUBRIQUE),
		$sHtmlDescr
	);
}

// Dans les sous-activit�s de type "lien", les param�tres sont stock�s s�par�s par des ";"
list($sLien,$iMode,$sIntitule) = explode(";",$oSousActiv->retDonnees());

// ---------------------
// Lien
// ---------------------
$sHtmlLien = NULL;
$sLien = trim($sLien);

if (!empty($sLien))
{
	// Pour une ouverture vers un site ext�rieur, il faut ajouter "http://"
	switch ($oSousActiv->retType())
	{
		case LIEN_SITE_INTERNET:
			if (!strstr($sLien,"http://"))
				$sLien = "http://{$sLien}";
			break;
			
		case LIEN_DOCUMENT_TELECHARGER:
			if ($iMode == FRAME_CENTRALE_INDIRECT)
				$iMode = MODE_LIEN_TELECHARGER;
			break;
	}
	
	$sHtmlLien = $oProjet->retLien($sLien,$sIntitule,$iMode);
}

$oProjet->terminer();

?>
<html>
<head>
<?php inserer_feuille_style("zdc_frame_principale.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('sous_activ.js')?>"></script>
</head>
<body>
<p>&nbsp;</p>
<table border="0" cellpadding="5" cellspacing="0" align="center" width="70%">
<?php echo $sHtmlDescr; ?>
<tr><td class="cellule_lien"><?php echo $sHtmlLien; ?></td></tr>
</body>
</html>

