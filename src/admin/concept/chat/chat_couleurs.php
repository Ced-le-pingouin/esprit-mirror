<?php

require_once("globals.inc.php");

if (isset($HTTP_GET_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_GET_VARS["CouleurChat"];
else if (isset($HTTP_POST_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_POST_VARS["CouleurChat"];
else
	$url_sCouleurChat = 0;

$asTableau = file("couleurs.txt");

$sListeCouleurs = NULL;

while (@list(,$sLigne) = each($asTableau))
{
	$sLigne = trim($sLigne);
	
	list($sNomCouleur,$sValeurCouleur) = split(";",$sLigne);
	
	$sListeCouleurs .= "<tr>"
		."<td width=\"1%\" style=\"background-color: #FFFFFF\">"
		."<input"
		." type=\"radio\""
		." name=\"CouleurChat\""
		." value=\"{$sLigne}\""
		." onfocus=\"blur()\""
		.($url_sCouleurChat == $sLigne ? " checked" : NULL)
		.">"
		."</td>"
		."<td style=\"background-color: #FFFFFF\"><b>{$sNomCouleur}</b></td>"
		."<td width=\"50%\" style=\"background-color: rgb({$sValeurCouleur});\">&nbsp;</td>"
		."</tr>\n";
}

?>
<html>
<head>
<?php inserer_feuille_style("chat.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function ChangerCouleur()
{
	var obj = document.forms[0].elements["CouleurChat"];
	
	for (i=0; i<obj.length; i++)
		if (obj[i].checked)
			break;
	top.opener.ChangerCouleur(obj[i].value);
	top.close();
}

//-->
</script>
</head>
<body class="couleurs">
<form>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td style="background-color: #222222;">
<table border="0" cellpadding="3" cellspacing="1" width="100%">
<?=$sListeCouleurs?>
</table>
</td></tr></table>
</form>
</body>
</html>
