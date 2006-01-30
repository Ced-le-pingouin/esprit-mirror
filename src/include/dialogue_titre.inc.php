<html>
<head>
<?php 

lierFichiersCSS("dialog.css");

if (!isset($sDialogueTitre))
	$sDialogueTitre = "Bo&icirc;te de dialogue sans titre";

if (isset($sDialogueLogo))
	$sDialogueLogo = "<img src=\"".dir_theme($sDialogueLogo)."\" border=\"0\">";
else
	$sDialogueLogo = "&nbsp;";
?>
</head>
<body class="haut">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="1%" align="left" valign="top"><?=$sDialogueLogo?></td>
</tr>
</table>
<div class="dialog_titre_principal">&nbsp;<?=$sDialogueTitre?></div>
<div class="dialog_sous_titre"><?=$sDialogueSousTitre?></div>
</body>
</html>
