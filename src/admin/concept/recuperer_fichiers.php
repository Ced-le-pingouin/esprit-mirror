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
** Fichier ................: recuperer_fichiers.php
** Description ............: 
** Date de création .......: 22-08-2002
** Dernière modification ..: 10-10-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

if (!empty ($_GET))
{
	$IdForm  = $_GET["FORM"];
	$IdActiv = $_GET["ACTIV"];
}
else
	$IdForm = $IdActiv = NULL;

require_once ("globals.inc.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<title>Récupérer des fichiers relatifs à ce bloc</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?=inserer_feuille_style()?>

<script type="text/javascript" language="javascript">
<!--

function init(v_iLargeur,v_iHauteur)
{
	if (v_iHauteur <= 0)
		if (document.all)
			v_iHauteur = document.body.scrollHeight+30;
		else
			v_iHauteur = document.body.offsetHeight+50;
		
	var iCentrerLargeur = ((screen.width-v_iLargeur)/2);
	var iCentrerHauteur = ((screen.height-v_iHauteur)/2);
	
	self.moveTo(iCentrerLargeur,iCentrerHauteur);
	self.resizeTo(v_iLargeur,v_iHauteur);
	self.focus();
}

//-->
</script>

</head>

<body>
<?php 

?>
<form name="FRM_RECUPERER_FICHIERS" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<p>Choisissez dans la liste ci-dessous, le fichier que vous désirez récupérer.</p>

<iframe src="recuperer_fichiers_liste.php<?php echo "?FORM=$IdForm&ACTIV=$IdActiv"; ?>" 
	name="FRAME_LISTE"
	frameborder="0"
	align="center"
	width="99%"
	height="350"
	frameborder="1"
	scrolling="yes"
	style="border: #999999 solid 1px;">
</iframe>
<div style="text-align: right;">
<hr>
<!--<input type="button" value="Récupérer" onclick="frames[0].document.forms[0].submit()">-->
<input type="button" value="Rafra&icirc;chir" onclick="top.frames['FRAME_LISTE'].location.reload(true)">
<input type="button" value="Fermer" onclick="self.close()">
</div>

</form>

</body>

</html>
