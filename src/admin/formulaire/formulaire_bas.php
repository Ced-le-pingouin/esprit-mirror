<?php
require_once("globals.inc.php");

if (!empty($HTTP_GET_VARS["idformulaire"]))
	$iIdFormulaire = $HTTP_GET_VARS["idformulaire"];
else
	$iIdFormulaire = 0;
?>
<html>
<head>
<script type="text/javascript" language="javascript" src="<?=dir_javascript("window.js")?>"></script>
<script type="text/javascript">
function modifaxeform(idformulaire)
{
	PopupCenter('formulaire_axe_index.php?idformulaire='+idformulaire,'WinModifAxesForm',450,300,'location=no,status=no,toolbar=no,scrollbars=yes');
}
</script>
<?php inserer_feuille_style("menu.css; dialog-menu.css"); ?>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="2" width="100%" height="100%">
<tr>
<td width="1">
	&nbsp;<a href="javascript: void(0);" onClick="parent.FORMFRAMELISTE.location.replace('ajouter_formulaire.php');">Nouveau&nbsp;formulaire</a>&nbsp;
</td>
<td width="1">&nbsp;<?php
	if ($iIdFormulaire)
		echo "<a href=\"javascript: void(0);\" onClick=\"modifaxeform($iIdFormulaire)\" id=\"btnDefAxes\">Définir&nbsp;les&nbsp;axes&nbsp;du&nbsp;formulaire</a>&nbsp;";
	?></td>
<td style="text-align: center;"><span id="id_status">&nbsp;</span></td>
<td align="right" width="1">
	&nbsp;<a href="javascript: void(0);" onclick="top.close()" onfocus="blur()">Fermer</a>&nbsp;
</td>
</tr>
</table>
</body>
</html>
