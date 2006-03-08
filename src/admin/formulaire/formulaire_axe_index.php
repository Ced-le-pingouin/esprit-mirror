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

//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{

	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
}
else if (isset($HTTP_POST_VARS))
{

	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
}
else
{

	$v_iIdFormulaire = 0;
}
$sAdressePage= "formulaire_axe.php?idformulaire=".$v_iIdFormulaire;

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Gestion des Axes</title>
</head>
</html>

<frameset rows="*,20" border="0" frameborder="0" framespacing="0">
<frame name="FORMFRAMEAXE" src="<?php echo "$sAdressePage"; ?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="yes" noresize>
<frame name="FORMFRAMEAXEMENU" src="formulaire_axe_menu.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
</frameset>

