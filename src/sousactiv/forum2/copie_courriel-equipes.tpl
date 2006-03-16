<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript">
<!--
function valider() { document.forms[0].submit(); }
function def_copie_courriel_nouv_sujets(v_iCopieTousMessages) { document.forms[0].elements["copieCourrielNouvSujets"].value = v_iCopieTousMessages; }
function init() { verif_checkbox_principal(document.forms[0].elements["idEquipes"]); }
//-->
</script>
<style type="text/css">
<!--
td.cellule_sous_titre { text-align: left; }
-->
</style>
</head>
<body onload="init()">
{html_form}
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td class="cellule_sous_titre"><input type="checkbox" name="idEquipes" onclick="select_deselect_checkbox(this)" onfocus="blur()"></td><td class="cellule_sous_titre" width="99%">Toutes les &eacute;quipes</td></tr>
[BLOCK_EQUIPE+]
<tr><td><input type="checkbox" name="idEquipes[]" value="{equipe->id}" onclick="verif_checkbox_principal(this)" onfocus="blur()"{equipe->selectionne}></td><td>{equipe->nom}</td></tr>
[BLOCK_EQUIPE-]
</table>
<input type="hidden" name="idForum" value="{forum->id}">
{/html_form}
</body>
</html>

