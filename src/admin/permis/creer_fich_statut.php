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
** Fichier ................: creer_fich_statut.php
** Description ............: 
** Date de création .......: 03/09/2002
** Dernière modification ..: 09/07/2004
** Auteurs ................: Jérôme TOUZE, Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once ("globals.inc.php");

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_STATUT");

if (isset($HTTP_POST_VARS["StatutForm"]) &&
	$HTTP_POST_VARS["StatutForm"] == "creer")
	$oProjet->creerFichierStatut();

$oProjet->terminer();
?>
<html>
<head>
<title>Cr&eacute;ation du fichier des statuts</title>
<?=inserer_feuille_style()?>
<style type="text/css">
<!--
body { background-image: none; }
-->
</style>
</head>
<body>
<div style="position: absolute; left: 0px; top: 0px; width: 200px; height: 100px;">
<form action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" name="formulaire" method="post">
<table border="0" cellspacing="1" cellpadding="5" width="100%" height="100%">
<tr><td colspan="2" align="center" style="background-color: rgb(250,250,251); font-weight: bold;">Voulez-vous cr&eacute;er le fichier statut.def.php&nbsp;?</td></tr>
<tr><td align="center" class="cellule_sous_titre"><a href="javascript: document.forms[0].submit(); close();">Oui</a></td><td align="center" class="cellule_sous_titre"><a href="javascript: close()">Non</a></td></tr>
</table>
<input type="hidden" name="StatutForm" value="creer">
</form>
</div>
</body>
</html>

