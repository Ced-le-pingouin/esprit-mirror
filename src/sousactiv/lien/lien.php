<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: lien.php
** Description ............: 
** Date de création .......: 22/04/2002
** Dernière modification ..: 21/11/2005
** Auteurs ................: Filippo Porco <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
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

// Dans les sous-activités de type "lien", les paramètres sont stockés séparés par des ";"
list($sLien,$iMode,$sIntitule) = explode(";",$oSousActiv->retDonnees());

// ---------------------
// Lien
// ---------------------
$sHtmlLien = NULL;
$sLien = trim($sLien);

if (!empty($sLien))
{
	// Pour une ouverture vers un site extérieur, il faut ajouter "http://"
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
<meta http-equiv="content-type" content="text/html; charset=utf-8">
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

