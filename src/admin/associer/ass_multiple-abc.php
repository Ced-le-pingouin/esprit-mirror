<?php require_once("globals.inc.php"); ?>
<html>
<head>
<?=inserer_feuille_style()?>
<script type="text/javascript" language="language" src="<?=dir_javascript('globals.js')?>"></script>
<script type="text/javascript" language="language" src="ass_multiple.js"></script>
<script type="text/javascript" language="language">
<!--
var g_sRech = null;
//-->
</script>
<style type="text/css">
<!--
input { width: 30px; }
td.cellule_sous_titre { height: 20px; }
-->
</style>
</head>
<body>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
<tr><td>&nbsp;</td></tr>
<?php
{
	$l = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	while ($i<strlen($l)) {
		echo "<tr><td class=\"cellule_sous_titre\" align=\"center\">&nbsp;<a href=\"javascript: sePlacerPersonne('$l[$i]',oFramePersonnes());\">$l[$i]</a>&nbsp;</td></tr>";
		$i++;
	}
}
?>
</table>
</body>
</html>
