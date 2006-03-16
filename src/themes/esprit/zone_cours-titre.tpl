<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://zone_menu-titre.css">
<script type="text/javascript" language="javascript" src="theme://propos/propos.js"></script>
<style type="text/css">
<!--

body
{
	background: rgb(255,255,255) url("theme://fond-titre-zdc.gif") repeat-x;
}

.infos_utilisateur { color: rgb(0,0,0); }

//-->
</style>
<script type="text/javascript" language="javascript">
<!--
function changerHistorique(v_sHistorique)
{
	if (document.getElementById && document.getElementById("historique"))
		if (v_sHistorique == "&nbsp;")
		{
			document.getElementById("indice").innerHTML = "&nbsp;";
			document.getElementById("historique").innerHTML = "&nbsp;";
		}
		else
		{
			document.getElementById("indice").innerHTML = "<img src='theme://indice.gif' width='8' height='12' border='0'>";
			document.getElementById("historique").innerHTML = unescape(v_sHistorique);
		}
	else
		setTimeout("changerHistorique('" + v_sHistorique + "')",1000);
}
//-->
</script>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td><a href="javascript: void(propos('theme://'));" onfocus="blur()" title="A propos de la conception d'Esprit"><img src="theme://logo-titre-zdc.gif" width="225" height="116" border="0"></a></td>
</tr>
</table>
<div class="infos_utilisateur">{infos_utilisateur}&nbsp;</div>
<div class="sous_titre">Zone&nbsp;de&nbsp;cours</div>
<div class="titre_formation"><span class="titre_formation">{titre_formation}</span>&nbsp;&#187;&nbsp;{titre_cours}&nbsp;&#187;&nbsp;<span class="titre_unite">{titre_unite}</span></div>
<div class="historique">
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<td id="indice">&nbsp;</td>
<td><span id="historique" class="historique">&nbsp;</span></td>
</tr></table>
</div>
</body>
</html>
