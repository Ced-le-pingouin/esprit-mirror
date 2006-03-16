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

require_once ("globals.inc.php");
require_once (dir_database ("evenement.tbl.php"));

$oProjet = new CProjet();

$ev = new CEvenement($oProjet->oBdd);

$a = $ev->retListeConnectes();

?>

<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>

<style type="text/css">
<!--

body, div
{
	font-size: 7pt;
	background-image: none;
	background-color: #FFFFE7;
	text-align: center;
}

//-->
</style>

<script language="javascript" type="text/javascript">
<!--

var IdTimeOut=null;

function rechargerPage()
{
	document.location = document.location;
}

IdTimeOut = window.setTimeout("rechargerPage()",30000);

//-->
</script>

</head>

<body>

<div style="text-align: centre;">
<?php

for ($i=0; $i<count ($a); $i++)
	echo $a[$i]."<br>\n";

?>
</div>

<br>

<div style="text-align: right;"><a href="javascript: self.location.reload();">Rafraichir</a></div>

<a name="fin">

</body>

</html>
