<html>
<head>
<TITLE>Axes/Tendances</TITLE>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="{chemin_windows.js}"></script>
<script type="text/javascript">
<!--
function gestionaxes()
{
	PopupCenterOffset('gestion_axes_index.php?idformulaire='+{id_formulaire},'WinGestionAxes',450,300,'location=no,status=no,toolbar=no,scrollbars=yes',20,20);
}
//-->
</script>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">
</head>

<body class="popup" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">

<TABLE border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td align="left" valign="top">
		<FORM NAME="formaxe" ACTION="formulaire_axe.php" method ="GET" onLoad="self.focus()">
		<fieldset><legend><b>Sélection des "axes/tendances" applicables au formulaire</b></legend>
		[BLOCK_AXES+]
		<INPUT TYPE="checkbox" name="axes[]" value="{id_axe}" {chk}>{couleur_police1}{desc_axe}{couleur_police2}<br>
		[BLOCK_AXES-]
		</SELECT>
		<br><br>
		&nbsp
		<a href="#" onclick="gestionaxes();">Gestion des axes (ajout/suppression)</a>
		<br><br>
		<INPUT TYPE="hidden" name="idformulaire" value="{id_formulaire}">
		<INPUT TYPE="hidden" VALUE="valider" name="valider">
		</fieldset>
		</FORM>
		<br>
		<div align="center">
		Les axes présents actuellement dans le formulaire sont écrits en <i>italique</i>
		</div>
</td></tr>
</table>

</body>
</html>

