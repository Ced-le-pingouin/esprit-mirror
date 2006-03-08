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

//Ceci est ajouté uniquement pour pouvoir effectuer un contrôle de l'utilisateur
require_once("globals.inc.php");
$oProjet = new CProjet();
if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{

//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
}
?>

<html>

<head>
<script type="text/javascript" language="javascript" src="<?=dir_javascript("window.js")?>"></script>
<script type="text/javascript">
<!--
function ajoutobj(idformulaire)
{
	PopupCenter('formulaire_modif_ajout.php?idformulaire='+idformulaire,'WinAjoutObjForm',450,150,'location=no,status=no,toolbar=no,scrollbars=no');
}

function supobj(idobj,idformulaire) 
{
	if (confirm('Voulez-vous supprimer l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_sup.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}
	
function modifposobj(idobj,idformulaire)
{
	PopupCenter('position_objet.php?idobj='+idobj+'&idformulaire='+idformulaire,'WinModifPosObjForm',300,150,'location=no,status=no,toolbar=no,scrollbars=no');
}

function copieobj(idobj,idformulaire) 
{
	if (confirm('Voulez-vous copier l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_copie.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}

function modifaxeform(idformulaire)
{
	PopupCenter('formulaire_axe_index.php?idformulaire='+idformulaire,'WinModifAxesForm',450,300,'location=no,status=no,toolbar=no,scrollbars=yes');
}
//-->
</script>


<link type="text/css" rel="stylesheet" href="<?=dir_theme("formulaire/formulaire.css");?>">

</head>

<?php
echo "<body class=\"menumodif\">\n";
echo "<TABLE style=\"border-top:1px solid black; border-bottom:1px solid black\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";

echo "<tr><td style=\"text-align : left\">&nbsp\n";

echo "Elément : ";

if ($v_iIdFormulaire > 0)
{
	//echo "<a href=\"javascript: ajoutobj($v_iIdFormulaire);\">Ajouter un élément</a>\n";
	echo "<a href=\"javascript: ajoutobj($v_iIdFormulaire);\">Ajouter</a>\n";
}
else
{
	echo "<font color = red>";
	echo "Ajouter"; // un élément";
	echo "</font>\n";
}

echo " - ";

if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire uniquement pour pouvoir recharger la liste après la suppression
{
	//echo "<a href=\"javascript: supobj($v_iIdObjForm,$v_iIdFormulaire);\">Supprimer l'élément</a>\n";
	echo "<a href=\"javascript: supobj($v_iIdObjForm,$v_iIdFormulaire);\">Supprimer</a>\n";
}
else
{
	echo "<font color = red>";
	echo "Supprimer"; // l'élément";
	echo "</font>\n";
}

echo " - ";

if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
{
	//echo "<a href=\"javascript: modifposobj($v_iIdObjForm,$v_iIdFormulaire);\">Déplacer l'élément</a>\n";
	echo "<a href=\"javascript: modifposobj($v_iIdObjForm,$v_iIdFormulaire);\">Déplacer</a>\n";
}
else
{
	echo "<font color = red>";
	echo "Déplacer";// l'élément";
	echo "</font>\n";
}

echo " - ";

if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
{
	//echo "<a href=\"javascript: modifposobj($v_iIdObjForm,$v_iIdFormulaire);\">Copier l'élément</a>\n";
	echo "<a href=\"javascript: copieobj($v_iIdObjForm,$v_iIdFormulaire);\">Copier</a>\n";
}
else
{
	echo "<font color = red>";
	echo "Copier";// l'élément";
	echo "</font>\n";
}

echo "</td><td style=\"text-align : right\">";


if ($v_iIdFormulaire > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
{
	echo "<a href=\"javascript: modifaxeform($v_iIdFormulaire);\">Définir les axes de ce formulaire</a>\n";
}
else
{
	echo "<font color = red>";
	echo "Définir les axes de ce formulaire";
	echo "</font>\n";
}

echo "&nbsp</td></tr>\n";
echo "</TABLE>\n";
echo "</body>\n";
}//Verification de la permission d'utiliser le concepteur de formulaire
?>

