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
** Fichier ................: ressource_deposer.php
** Description ............: Ouvre une fenêtre de dialogue et demande le titre
**                           du documents, une description et sauvegarde le
**                           tout dans la base de données.
** Date de création .......: 03/07/2001
** Dernière modification ..: 15/12/2003
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// *************************************
// Déclaration du projet
// *************************************

$oProjet = new CProjet();

// *************************************
// Initialisations
// *************************************

$oProjet->initSousActivCourante();

// R E M A R Q U E :
// Ne pas déplacer cette ligne. Le fichier "ressource_deposer.inc.php" 
// a besoin de la variable "$oProjet"
include_once("ressource_deposer.inc.php");

?>
<html>
<head>
<title></title>
<?php inserer_feuille_style("dialog.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function envoyer()
{
	document.getElementById('idForm').style.visibility = 'hidden';
	document.getElementById('idGauge').style.visibility = 'visible';
	document.forms[0].submit();
}

//-->
</script>

<style type="text/css">
<!--

body {background-image: none; }
.divForm, .divGauge
{
	position: absolute;
	top: 5px;
	left: 10px;
	width: 530px;
	height: 340px;
}

.divForm { visibility: visible; }
.divGauge { visibility: hidden; }

//-->
</style>
</head>
<body>
<div id="idForm" class="divForm">
<form action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<span class="intitule">Titre du fichier&nbsp;:</span><br>
<input name="TITRE_FICHIER" type="text" size="70" maxlength="81" value="" style="width: 100%">
<span class="intitule">Texte associ&eacute;&nbsp;:</span><br><textarea name="DESCRIPTION_FICHIER" cols="67" rows="12" style="width: 100%"></textarea>
<input type="file" name="FICHIER" size="55" style="width: 100%">
</form>
</div>
<div id="idGauge" class="divGauge">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
<td align="center"><img src="<?=dir_theme('barre-de-progression.gif')?>"><br>Un instant s.v.p.</td>
</tr></table>
</div>
</body>
</html>
<?php $oProjet->terminer(); ?>
