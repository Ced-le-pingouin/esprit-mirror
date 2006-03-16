<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://dialog.css">
<link type="text/css" rel="stylesheet" href="theme://statuts.css">
</head>
<body>
<form action="changer_statut.php" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td>&nbsp;</td><td colspan="3"><b>Cliquez le statut que vous d&eacute;sirez utiliser et validez ensuite votre choix.</b></td></tr>
<tr><td colspan="4">&nbsp;</td></tr>
[BLOCK_LISTE_STATUTS+]
<tr><td>&nbsp;</td><td><img src="commun://espacer.gif" width="20" height="1" alt="" border="0"></td><td><input type="radio" name="{nom_radio_statut}" value="{valeur_statut}" onfocus="blur()"{selectionner_statut}></td><td width="99%">&nbsp;&nbsp;{nom_statut}</td></tr>
[BLOCK_LISTE_STATUTS-]
</table>
</form>
</body>
</html>

