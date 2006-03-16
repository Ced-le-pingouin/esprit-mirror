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

$sTitreFenetre = "Gestion des équipes";

$sParamsUrl = NULL;

$asParams = array("ACTION","ID_EQUIPE","NIVEAU","ID_NIVEAU");

foreach ($asParams as $sParam)
	if (isset($HTTP_POST_VARS[$sParam]))
		$sParamsUrl .= (isset($sParamsUrl) ? "&" : NULL)."$sParam=".$HTTP_POST_VARS[$sParam];

if (isset($sParamsUrl))
	$sParamsUrl = "?{$sParamsUrl}";

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?=$sTitreFenetre?></title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return top.frames["principal"]; }
function oMenu() { return top.frames["menu"]; }
//-->
</script>
</head>
</html>
<frameset rows="*,24" border="0" frameborder="0" framespacing="0">
<frame name="principal" src="equipe.php<?=$sParamsUrl?>" scrolling="no" noresize="1">
<frame name="menu" src="equipe_menu.php<?=$sParamsUrl?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="1">
</frameset>

