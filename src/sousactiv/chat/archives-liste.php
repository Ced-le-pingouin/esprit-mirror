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
** Fichier ................: archives-liste.php
** Description ............: 
** Date de création .......: 01/03/2001
** Dernière modification ..: 03/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("ids.class.php"));
require_once(dir_database("chat.tbl.php"));
require_once("archive.class.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdChat     = (empty($_GET["idChat"]) ? 0 : $_GET["idChat"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);
$url_iIdPers     = (empty($_GET["idPers"]) ? 0 : $_GET["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$oChat = new CChat($oProjet->oBdd,$url_iIdChat);

$bPeutEffacerArchive = $oChat->peutEffacerArchives($oProjet->retStatutUtilisateur());

// Répertoire des archives
$oIds  = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);
$amParams = array("idForm" => $oIds->retIdForm()
	, "idActiv" => $oIds->retIdActiv()
);
$sArchivesRep = dir_chat_archives($url_iTypeNiveau,$amParams,NULL,TRUE);
unset($oIds);

// ---------------------
// Effacer les archives
// ---------------------
if (isset($_POST["archives"]))
	foreach ($_POST["archives"] as $sArchiveNom)
		@unlink($sArchivesRep.$sArchiveNom);

// ---------------------
// Rechercher le nom de l'équipe
// ---------------------
$sEquipeNom = NULL;

if (CHAT_PAR_EQUIPE == $oChat->retModalite())
{
	$oEquipe = new CEquipe($oProjet->oBdd,$url_iIdEquipe);
	$sEquipeNom = $oEquipe->retNom();
	unset($oEquipe);
}

// {{{ Rechercher toutes les archives
$sFiltre = retIdUniqueChat($url_iIdChat,urlencode($sEquipeNom));

// Permet de rechercher toutes les archives de toutes les équipes d'un chat
// par équipe
if ($url_iIdEquipe == 0 && $url_iIdPers > 0)
	$sFiltre = substr($sFiltre,1);

$oArchives = new CArchives($sArchivesRep);
$oArchives->defFiltre($sFiltre);
$aoArchives = $oArchives->retArchives(TRUE);
// }}}

$sArchivesParams = NULL;

$bStatutHaut = retHautStatut($oProjet->retStatutUtilisateur());

$oTpl = new Template(dir_theme("dialog_menu-bloc.tpl",FALSE,TRUE));

$oTpl->remplacer("{dialog_menu_titre_attribut}"," colspan=\"2\"");
$oTpl->remplacer("{dialog_menu_titre}","Liste des conversations");

$oBlockRowIntitule = new TPL_Block("BLOCK_MENU_INTITULE",$oTpl);

$oBlockRowIntitule->beginLoop();

$iNbArchives = count($aoArchives)-1;

for ($i=$iNbArchives; $i>=0; $i--)
{
	if (!$bStatutHaut
		&& urldecode($aoArchives[$i]->retEquipe()) != $sEquipeNom)
		continue;
	
	if ($i == $iNbArchives)
		$sArchivesParams .= "?idNiveau={$url_iIdNiveau}"
			."&typeNiveau={$url_iTypeNiveau}"
			."&archive=".rawurlencode($aoArchives[$i]->retNomArchive())
			.($url_iIdPers > 0 ? "&idPers={$url_iIdPers}" : NULL);
	
	$sCell_1 = ($bPeutEffacerArchive ? "<input type=\"checkbox\" name=\"archives[]\" value=\"".$aoArchives[$i]->retNomArchive()."\">" : "&nbsp;");
	
	$sCell_2 = "<a"
		." href=\"archives.php"
			."?idNiveau={$url_iIdNiveau}"
			."&typeNiveau={$url_iTypeNiveau}"
			."&archive=".rawurlencode($aoArchives[$i]->retNomArchive())
			.($url_iIdPers > 0 ? "&idPers={$url_iIdPers}" : NULL)
		."\""
		." target=\"Principale\""
		." onfocus=\"blur()\""
		.">"
		."Chat du ".$aoArchives[$i]->retDate()
		."<br><small>(".$aoArchives[$i]->retHeureCourte().")</small>"
		."</a>";
	
	$oBlockRowIntitule->nextLoop();
	
	$oBlockCellIntitule = new TPL_Block("BLOCK_MENU_CELL_INTITULE",$oBlockRowIntitule);
	$oBlockCellIntitule->beginLoop();
	
	$oBlockCellIntitule->nextLoop();
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule_attribut}",NULL);
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule}",$sCell_1);
	
	$oBlockCellIntitule->nextLoop();
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule_attribut}"," width=\"99%\"");
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule}",$sCell_2);
	
	$oBlockCellIntitule->afficher();
}

// {{{ Pas d'archive trouvé
if ($iNbArchives == -1)
{
	$oBlockRowIntitule->nextLoop();
	
	$oBlockCellIntitule = new TPL_Block("BLOCK_MENU_CELL_INTITULE",$oBlockRowIntitule);
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule_attribut}"," colspan=\"2\"");
	$oBlockCellIntitule->remplacer("{dialog_menu_intitule}","<small>Pas d'archive trouv&eacute;e</small>");
	$oBlockCellIntitule->afficher();
}
// }}}

$oBlockRowIntitule->afficher();

// Menu
$sMenu = "<a"
	." href=\"javascript: self.location = self.location;\""
	.">Rafra&icirc;chir</a>"
	.($bPeutEffacerArchive && $iNbArchives > -1 ? "&nbsp;|&nbsp;<a href=\"javascript: effacerArchives();\">Supprimer</a>" : NULL);

$oBlockMenu = new TPL_Block("BLOCK_MENU",$oTpl);

$oBlockMenu->remplacer("{dialog_menu_attribut}"," colspan=\"2\"");
$oBlockMenu->remplacer("{dialog_menu}",$sMenu);

$oBlockMenu->afficher();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("commun/dialog.css; chat.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function init()
{
	top.oPrincipal().location = "archives.php<?php echo $sArchivesParams?>";
}

function effacerArchives()
{
	var bOk = false;
	
	if (typeof(document.forms[0].elements["archives[]"].length) == "undefined")
		bOk = document.forms[0].elements["archives[]"].checked;
	else
	{
		var iTotal = document.forms[0].elements["archives[]"].length;
		
		for (i=0; i<iTotal; i++)
			if (document.forms[0].elements["archives[]"][i].checked)
			{
				bOk = true;
				break;
			}
	}
	
	if (bOk)
	{
		if (confirm("Vous êtes-vous sur le point d'effacer ces archives.\nVoulez-vous continuer ?"))
			document.forms[0].submit();
	}
	else
		alert("Vous devez sélectionner au moins une archive avant de supprimer");
}

//-->
</script>

</head>
<body class="gauche" onload="init()">
<form action="<?php echo $_SERVER['PHP_SELF']."?idNiveau={$url_iIdNiveau}&typeNiveau={$url_iTypeNiveau}&idChat={$url_iIdChat}&idEquipe={$url_iIdEquipe}"?>" method="post">
<?php $oTpl->afficher(); ?>
</form>
</body>
</html>
