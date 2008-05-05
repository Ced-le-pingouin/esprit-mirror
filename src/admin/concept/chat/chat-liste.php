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

require_once("globals.inc.php");
require_once(dir_database("chat.tbl.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (isset($_GET["idNiveau"]))
	$url_iIdNiveau = $_GET["idNiveau"];
else if (isset($_POST["idNiveau"]))
	$url_iIdNiveau = $_POST["idNiveau"];
else
	$url_iIdNiveau = 0;

if (isset($_GET["typeNiveau"]))
	$url_iTypeNiveau = $_GET["typeNiveau"];
else if (isset($_POST["typeNiveau"]))
	$url_iTypeNiveau = $_POST["typeNiveau"];
else
	$url_iTypeNiveau = 0;

$url_iIdChat = (empty($_POST["idChat"]) ? 0 : $_POST["idChat"]);
$url_sAction = (empty($_POST["action"]) ? NULL : $_POST["action"]);

if ($url_sAction != "ajouter" && $url_iIdChat > 0)
{
	// Modifier/Supprimer un chat existant
	$oChat = new CChat($oProjet->oBdd,$url_iIdChat);
	
	if ($url_sAction == "supprimer")
	{
		$oChat->effacer();
		$url_iIdChat = 0;
	}
	else
	{
		$oChat->defNumOrdre($_POST["ordreChat"]);
		$oChat->defNom($_POST["nomChat"]);
		$oChat->defCouleur($_POST["couleurChat"]);
		$oChat->defModalite($_POST["modaliteChat"]);
		$oChat->defEnregConversation($_POST["enregistrerChat"]);
		$oChat->defSalonPrive($_POST["utiliserSalonPriveChat"]);
		
		$oChat->enregistrer();
	}
}

// ---------------------
// Liste des salons
// ---------------------
if ($url_iTypeNiveau == TYPE_SOUS_ACTIVITE)
	$oObjNiveau = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
else if ($url_iTypeNiveau == TYPE_RUBRIQUE)
	$oObjNiveau = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);

$iNbChats = $oObjNiveau->initChats();

if ($iNbChats == 0 || $url_sAction == "ajouter")
{
	$url_iIdChat = $oObjNiveau->ajouterChat();
	$iNbChats = $oObjNiveau->initChats();
}

// ---------------------
// Template
// ---------------------
$sTableChats = NULL;

$oTpl = new Template(dir_theme("dialog_menu-bloc.tpl",FALSE,TRUE));

$oTpl->remplacer("{dialog_menu_titre}","Liste des \"chat\"");
$oTpl->remplacer("{dialog_menu_titre_attribut}"," colspan=\"2\"");

$oBlock_Intitule = new TPL_Block("BLOCK_MENU_INTITULE",$oTpl);
$oBlock_Menu = new TPL_Block("BLOCK_MENU",$oTpl);

$oBlock_Intitule->beginLoop();

foreach ($oObjNiveau->aoChats as $oChat)
{
	$iId = $oChat->retId();
	
	if ($url_iIdChat == 0)
		$url_iIdChat = $iId;
	
	$sInputRadio = "<input"
		." type=\"radio\""
		." name=\"idChat\""
		." value=\"{$iId}\""
		." onclick=\"top.AfficherSalon('{$iId}')\""
		." onfocus=\"blur()\""
		.($url_iIdChat == $iId ? " checked" : NULL)
		.">";
	
	$oBlock_Intitule->nextLoop();
	
	$oBlock_Cell_Intitule = new TPL_Block("BLOCK_MENU_CELL_INTITULE",$oBlock_Intitule);
	
	$oBlock_Cell_Intitule->beginLoop();
	
	$oBlock_Cell_Intitule->nextLoop();
	$oBlock_Cell_Intitule->remplacer("{dialog_menu_intitule_attribut}"," width=\"1%\"");
	$oBlock_Cell_Intitule->remplacer("{dialog_menu_intitule}",$sInputRadio);
	
	$oBlock_Cell_Intitule->nextLoop();
	$oBlock_Cell_Intitule->remplacer("{dialog_menu_intitule_attribut}","");
	$oBlock_Cell_Intitule->remplacer("{dialog_menu_intitule}",$oChat->retNom());
	
	$oBlock_Cell_Intitule->afficher();
}

$oBlock_Intitule->afficher();

// Ajouter le bouton "Ajouter"
$sMenu = "<a href=\"javascript: ajouter();\" onfocus=\"blur()\">Ajouter</a>";

// Ajouter le bouton "Supprimer"
if ($iNbChats > 0)
	$sMenu .= "&nbsp;|&nbsp;<a href=\"javascript: supprimer();\" onfocus=\"blur()\">Supprimer</a>";

$oBlock_Menu->remplacer("{dialog_menu_attribut}"," colspan=\"2\"");
$oBlock_Menu->remplacer("{dialog_menu}",$sMenu);
$oBlock_Menu->afficher();

$sFonctionInit = "top.AfficherSalon('{$url_iIdChat}');\n";

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("commun/dialog.css"); ?>
<script type="text/javascript" language="javascript">
<!--
function init()
{
	<?php echo $sFonctionInit?>
}

function envoyer()
{
	document.forms[0].submit()
}

function ajouter()
{
	document.forms[0].elements["action"].value = "ajouter";
	
	for (i=0; i<document.forms[0].elements["idChat"].length; i++)
		document.forms[0].elements["idChat"][i].selected = false;
	
	envoyer();
}

function supprimer()
{
	if (confirm("Êtes-vous certain de vouloir supprimer ce salon ?"))
	{
		document.forms[0].elements["action"].value = "supprimer";
		envoyer();
	}
}

//-->
</script>
</head>
<body class="gauche" onload="init()">
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<?php $oTpl->afficher(); ?>
<input type="hidden" name="action" value="">
<input type="hidden" name="idNiveau" value="<?php echo $url_iIdNiveau?>">
<input type="hidden" name="typeNiveau" value="<?php echo $url_iTypeNiveau?>">
</form>
</body>
</html>

