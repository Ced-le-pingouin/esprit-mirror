<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://exporter-personnes.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript">
<!--
function exporter()
{
	var oChamps = self.frames["CHAMPS"].document.forms[0].elements["CHAMPS[]"];
	
	for (i=0; i<oChamps.length; i++)
		if (oChamps[i].checked)
			document.forms[0].elements["LISTE_CHAMPS"].value += oChamps[i].value
				+ (i < oChamps.length-1 ? "," : "");
	
	document.forms[0].submit();
}
//-->
</script>
</head>
<body>
<form action="export-fichier.php" method="post">
<p>Sélectionnez dans la liste ci-dessous, les champs dont vous avez besoin.</p>
[BLOCK_ONGLET_CHAMPS+][BLOCK_ONGLET_CHAMPS-]
<br>
[BLOCK_ONGLET_TYPE_FICHIERS+][BLOCK_ONGLET_TYPE_FICHIERS-]
<input type="hidden" name="LISTE_CHAMPS" value="">
<input type="hidden" name="LISTE_IDPERS" value="{LISTE_IDPERS->value}">
</form>
</body>
</html>
[SET_CHAMPS_EXPORTER+]
<p>Sélectionner le champs &agrave; exporter&nbsp;:</p>
<iframe name="CHAMPS" src="exporter-liste_champs.php" width="100%" height="100" frameborder="0" marginwidth="5" marginheight="5"></iframe>
<table border="0" cellspacing="0" cellpadding="2">
<tr><td>&nbsp;<input type="checkbox" name="ENVOYER_NOMS_CHAMPS" checked></td><td>Envoyez-moi les noms des champs</td></tr>
</table>
[SET_CHAMPS_EXPORTER-]
[SET_TYPE_FICHIER+]
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr><td colspan="3">Vers quel type de fichier voulez-vous exporter&nbsp;:</td></tr>
<tr><td width="1%"><input type="radio" name="TYPE" value="csv" checked></td><td>Microsoft&copy; Excel (csv)</td><td>&nbsp;</td></tr>
<tr><td width="1%"><input type="radio" name="TYPE" value="html"></td><td>html</td><td>&nbsp;</td></tr>
</table>
[SET_TYPE_FICHIER-]
