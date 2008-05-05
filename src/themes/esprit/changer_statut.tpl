<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
<link type="text/css" rel="stylesheet" href="css://commun/globals.css" />
<link type="text/css" rel="stylesheet" href="css://commun/dialog.css" />
<title>Statuts</title>
</head>
<body class="statut">
<form action="changer_statut.php" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td>&nbsp;</td>
	<td colspan="3"><strong>Cliquez le statut que vous désirez utiliser et validez ensuite votre choix.</strong></td>
</tr>
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
[BLOCK_LISTE_STATUTS+]
<tr>
	<td>&nbsp;</td>
	<td><img src="commun://espacer.gif" width="20" height="1" alt="" border="0" /></td>
	<td><input type="radio" name="{nom_radio_statut}" value="{valeur_statut}" id="idstat{valeur_statut}" onfocus="blur()"{selectionner_statut} /></td>
	<td width="99%">&nbsp;&nbsp;<label for="idstat{valeur_statut}">{nom_statut}</label></td>
</tr>
[BLOCK_LISTE_STATUTS-]
</table>
</form>
</body>
</html>
