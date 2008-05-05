<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">

<script type="text/javascript" language="javascript">
<!--
function afficher_menu()
{
	top.frames["MENU"].location = "modifier-menu.php"
		+ "?modaliteFenetre={fenetre->modalite}"
		+ "&MENU=forum";
}
//-->
</script>
<style type="text/css">
<!--
.largeur_page { width: 100%; }
td.intitule { text-align: right; vertical-align: middle; }
-->
</style>
</head>
<body onload="afficher_menu()">
<form action="modifier_forum.php" method="post">
[BLOCK_FORUM+][BLOCK_FORUM-]
<input type="hidden" name="modaliteFenetre" value="{fenetre->modalite}">
<input type="hidden" name="idForum" value="{forum->id}">
<input type="hidden" name="idForumParent" value="{forum_parent->id}">
</form>
</body>
</html>

[SET_MODIFIER_FORUM+]
{onglet->forum}
[SET_MODIFIER_FORUM-]

[SET_ONGLET_FORUM+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td class="intitule">Nom&nbsp;:&nbsp;</td>
<td width="99%"><input type="text" class="largeur_page" name="nom_forum" size="50" value="{titre->valeur}"></td>
</tr>
<tr>
<td class="intitule">Modalit&eacute;&nbsp;:&nbsp;</td>
<td width="99%">
<select name="modalite_forum" style="width: 200px;">
<option value="3"{modalite->tous->selectionner}>Pour tous</option>
<option value="2"{modalite->equipe->selectionner}>Par &eacute;quipe</option>
</select>
</td>
</tr>
<tr>
<td class="intitule">Statut&nbsp;:&nbsp;</td>
<td width="99%">
<select name="statut_forum" style="width: 200px;">
<option value="{statut->ouvert->id}"{statut->ouvert->selectionner}>Ouvert</option>
<option value="{statut->consultable->id}"{statut->consultable->selectionner}>Consultable</option>
<option value="{statut->fermer->id}"{statut->fermer->selectionner}>Ferm&eacute;</option>
<option value="{statut->invisible->id}"{statut->invisible->selectionner}>Invisible</option>
</select>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="checkbox" name="accessible_visiteurs"{accessible_visiteurs->selectionner}>&nbsp;&nbsp;J'autorise les visiteurs de consulter ce forum</td>
</tr>
</table>
[SET_ONGLET_FORUM-]

[SET_SUPPRIMER_FORUM+]
<h3 class="attention" align="center">&Ecirc;tes-vous certain de vouloir supprimer ce sujet&nbsp;?</h3>
<p align="center">&laquo;&nbsp;<b>{sujet->titre}</b>&nbsp;&raquo;</p>
<p align="center">Ce sujet contient {messages->total} message(s).</p>
[SET_SUPPRIMER_FORUM-]
