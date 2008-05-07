<html>
<head>
<title>Choisir une formation</title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://formation.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript">
<!--
function changer_formation(v_iIdForm)
{
	if (top.opener && top.opener.choix_formation_callback)
	{
		top.opener.choix_formation_callback(v_iIdForm);
		top.close();
	}
}

function init() { location.hash = "formation_actuelle"; }
//-->
</script>
</head>
<body onload="init()" class="inscription">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
[BLOCK_MESSAGE+]
[VAR_SANS_FORMATION+]<tr><td><p>&nbsp;</p><p>&nbsp;</p><p class="bold_center">Aucune formation n'est disponible pour l'instant</p></td></tr>[VAR_SANS_FORMATION-]
[VAR_SELECTIONNER_FORMATION+]<tr><td>&nbsp;</td><td colspan="3"><div class="intitule">S&eacute;lectionnez une formation dans la liste ci-dessous&nbsp;:</div></td></tr>[VAR_SELECTIONNER_FORMATION-]
{message}
[BLOCK_MESSAGE-]
[BLOCK_LISTE_FORMATIONS+]
[VAR_FORMATION+]
<tr>
<td><img src="commun://espacer.gif" width="15" height="1" border="0"></td>
<td><img src="theme://boulet-13x13-1.gif" width="13" height="13" border="0"></td>
<td><img src="theme://espacer.gif" width="3" height="1" border="0"></td>
<td width="99%"><a href="javascript: void(0);" onclick="changer_formation('{formation.id}'); return false;" onfocus="blur()">{formation.nom}</a></td>
</tr>
[VAR_FORMATION-]
[VAR_FORMATION_ACTUELLE+]&nbsp;<img src="theme://icones/etoile.gif" width="13" height="13" border="0"><a name="formation_actuelle"></a>[VAR_FORMATION_ACTUELLE-]
{liste_formations}
[BLOCK_LISTE_FORMATIONS-]
</table>
</body>
</html>

