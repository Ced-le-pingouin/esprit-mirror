<?php require_once("globals.inc.php"); ?>
<html>
<head>
<?php inserer_feuille_style("dialog.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function rafraichir()
{
	var sLocation = self.location.href.replace(/#bottom/,"");
	self.location.replace(sLocation + "#bottom");
}

//-->
</script>
</head>
<body class="dialog_sous_menu">
<table border="0" cellpadding="3" cellspacing="1" width="100%" height="100%" class="dialog_sous_menu">
<tr>
<?php
if (isset($HTTP_GET_VARS["AM"]))
	echo "<td colspan=\"2\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().imprimer());\""
		." onfocus=\"blur()\""
		.">Imprimer</a>"
		."</td>"
		."<td align=\"right\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().telecharger());\""
		." target=\"_top\""
		." onfocus=\"blur()\""
		.">Sauver</a>"
		."</td>\n";
else
	echo "<td>&nbsp;</td>\n";
?>
</tr>
</table>
</body>
</html>
