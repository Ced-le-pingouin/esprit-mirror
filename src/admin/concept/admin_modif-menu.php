<?php

/*
** Fichier ................: admin_modif-menu.php
** Description ............: 
** Date de cr�ation .......: 25/04/2003
** Derni�re modification ..: 07/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iType   = (empty($HTTP_GET_VARS["type"]) ? 0 : $HTTP_GET_VARS["type"]);
$url_sParams = (empty($HTTP_GET_VARS["params"]) ? "0:0:0:0:0:0" : $HTTP_GET_VARS["params"]);

if ($url_iType == 0)
	$sCorpPage = "<div style=\"text-align: center;\">"
		."<b>e&nbsp;C&nbsp;O&nbsp;N&nbsp;C&nbsp;E&nbsp;P&nbsp;T</b>"
		."</div>";
else
	$sCorpPage = "<div style=\"text-align: right;\">"
		."<a href=\"javascript: "
		.($url_iType == TYPE_SOUS_ACTIVITE
			? "verifier()"
			: "envoyer()")
		.";\">Appliquer&nbsp;les&nbsp;changements</a>"
		."&nbsp;&nbsp;&#8226;&nbsp;&nbsp;"
		."<a href=\"javascript: annuler();\">Annuler</a>"
		."&nbsp;&nbsp;"
		."</div>";
?>
<html>
<head>
<?=inserer_feuille_style("concept.css")?>
<script type="text/javascript" language="javascript">
<!--
function verifier()
{
	if (top.frames["ADMINFRAMEMODIF"].type_different())
		envoyer();
}

function envoyer()
{
	top.frames["ADMINFRAMEMODIF"].document.forms[0].submit();
}

function annuler()
{
	top.frames["ADMINFRAMEMODIF"].location = "admin_modif.php"
		+ "?type=<?=$url_iType?>"
		+ "&params=<?=$url_sParams?>";
}
//-->
</script>
</head>
<body class="admin_modif_menu">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr><td><?=$sCorpPage?></td></tr>
</table>
</body>
</html>
