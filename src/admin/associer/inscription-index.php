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

$url_iIdForm = (isset($_GET["idform"]) ? $_GET["idform"] : 0);

$sTitrePrincipal = "Inscription";

$oProjet = new CProjet();
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);
$sNomFormation = $oFormation->retNom();
$oFormation = NULL;
$oProjet->terminer();

// ---------------------
// Template
// ---------------------
$sBlocHeadHtml = <<<BLOCK_HEAD_HTML
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>

<script type="text/javascript" language="javascript">
<!--
function choix_formation_callback(v_iIdForm)
{
	top.location = "{$_SERVER['PHP_SELF']}"
		+ "?idform=" + v_iIdForm;
}
//-->
</script>
BLOCK_HEAD_HTML;

$sFramePrincipal = "<frame"
	." src=\"inscription.php?idform={$url_iIdForm}\""
	." name=\"Principal\""
	." marginwidth=\"0\""
	." marginheight=\"0\""
	." frameborder=\"0\""
	." scrolling=\"yes\""
	." noresize=\"noresize\">";

$oTpl = new Template(dir_theme("dialog-index-2.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlocHeadHtml);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",emb_htmlentities("{$sTitrePrincipal} - {$sNomFormation}"));
$oTpl->remplacer("{frame_src_haut}","inscription-titre.php?TP=".rawurlencode($sTitrePrincipal)."&ST=".rawurlencode($sNomFormation));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","inscription-menu.php?tp=".rawurlencode($sTitrePrincipal));

$oTpl->afficher();

?>

