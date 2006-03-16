<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="language" src="global.js"></script>
<script type="text/javascript" language="language" src="ass_multiple.js"></script>
<script type="text/javascript" language="language">
<!--
function init()
{
	if (!top.opener || !top.opener.oFrmCours)
	{
		alert("Attention, cette fenêtre n'a plus de parenté avec"
			+ " la fenêtre des inscriptions."
			+ "\nNous vous conseillons de fermer cette fenêtre.");
			
		return;
	}
	
	with (top.opener.oFrmCours())
		location = location;
}
//-->
</script>
<style type="text/css">
<!--
body { background-image: none; }
-->
</style>
</head>
<body onload="init()">
<form>
[BLOCK_MODULE+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td class="cellule_sous_titre">&nbsp;#&nbsp;</td>
<td class="cellule_sous_titre"><input type="checkbox" name="ID_MOD[]" onclick="verif_inscrits_non_select()" value="{module->id}"></td>
<td class="cellule_sous_titre" colspan="2">&nbsp;&nbsp;<b>{module->intitule}</b></td>
</tr>
[BLOCK_PERSONNE_INSCRITE+]
<tr>
<td class="cellule_sous_titre">&nbsp;{personne->pos}&nbsp;</td>
<td class="{colonne->style}">&nbsp;</td>
<td class="{colonne->style}"><input type="checkbox" name="ID_MOD_{module->id}[]" onclick="verif_cours_non_select()" value="{personne->id}"></td>
<td class="{colonne->style}" width="99%">&nbsp;&nbsp;{personne->nom}</td>
</tr>
[BLOCK_PERSONNE_INSCRITE-]
<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="99%">&nbsp;</td></tr>
</table>
[BLOCK_MODULE-]
</form>
</body>
</html>
