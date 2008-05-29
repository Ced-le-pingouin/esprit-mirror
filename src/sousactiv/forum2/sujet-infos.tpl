<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/forum.css">
<script type="text/javascript" language="javascript">
<!--
function init() { 
{
	var iIdSujet = '{sujet->id}';
	top.oFrmMessages().location = "sujet-messages.php"
		+ "?idSujet=" + iIdSujet
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau
		+ "&idEquipe=" + top.g_iIdEquipe;
}
}
//-->
</script>
</head>
<body onload="init()" class="sujet_infos">
[BLOCK_INFOS_SUJET+]
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="50" class="sujet_infos">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr><td class="sujet_titre"><img src="commun://espacer.gif" width="15" height="20" border="0">{sujet->titre}</td></tr>
<tr><td class="sujet_infos">cr&eacute;&eacute; par <span title="{personne->nom_complet}" style="cursor: help; font-size: 7pt;">{personne->pseudo}</span>, le {sujet->date_creation}<img src="commun://espacer.gif" width="30" height="1" border="0"></td></tr>
</table>
</td>
</tr>
</table>
[BLOCK_INFOS_SUJET-]
</body>
</html>

