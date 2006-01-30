<?php require_once("globals.inc.php"); ?>
<html>
<head>
<?php inserer_feuille_style("dialog.css"); ?>
</head>
<body class="dialog_sous_menu">
<table border="0" cellpadding="3" cellspacing="0" width="100%" height="100%" class="dialog_sous_menu">
<tr>
<?php
if (isset($HTTP_GET_VARS["AM"]))
	echo "<td align=\"right\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().envoyer());\""
		." onfocus=\"blur()\""
		.">Appliquer les changements</a>"
		."&nbsp;&nbsp;&#8226;&nbsp;&nbsp;"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: top.oPrincipal().location = top.oPrincipal().location; void(0);\""
		." target=\"_top\""
		." onfocus=\"blur()\""
		.">Annuler</a>"
		."</td>\n";
else
	echo "<td>&nbsp;</td>\n";
?>
</tr>
</table>
</body>
</html>
