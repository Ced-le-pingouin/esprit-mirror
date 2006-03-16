<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="language" src="ass_multiple.js"></script>
<style type="text/css">
<!--
body { background-image: none; }
td.cellule_sous_titre { text-align: left; }
-->
</style>
</head>
<body onload="chargerPageInscrits('{formation->id}','{statut->id}')">
<a name="top"></a>
<form action="ass_multiple-pers.php?ID_FORM={formation->id}&STATUT_PERS={statut->id}" target="personnes" method="post">
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<tr>
<td class="cellule_sous_titre">&nbsp;#&nbsp;</td>
<td class="cellule_sous_titre"><input type="checkbox" name="ID_PERS" onclick="select_deselect_checkbox(this)" onfocus="blur()"></td>
<td class="cellule_sous_titre" width="99%">&nbsp;&nbsp;<a href="javascript: trier();">NOM&nbsp;et&nbsp;pr&eacute;nom&nbsp;<img src="{tri->image}" border="0"></a></td>
</tr>
[BLOCK_PERSONNE+]
<tr>
<td class="cellule_sous_titre"><a name="pos{personne->pos}"></a>&nbsp;{personne->pos}&nbsp;</style>
<td class="{colonne->style}"><input type="checkbox" name="ID_PERS[]" onclick="verif_checkbox_principal(this)" onfocus="blur()" value="{personne->id}"></td>
<td class="{colonne->style}">&nbsp;&nbsp;<span id="nom_{personne->pos}">{personne->nom}</span></td>
</tr>
[BLOCK_PERSONNE-]
</table>
<input type="hidden" name="ACTION" value="">
<input type="hidden" name="IDS_ACTION" value="">
<input type="hidden" name="TRI" value="{tri->mode}">
</form>
</body>
</html>
[SET_IMAGE_TRI_ASC+]theme://sort-incr.gif[SET_IMAGE_TRI_ASC-]
[SET_IMAGE_TRI_DESC+]theme://sort-desc.gif[SET_IMAGE_TRI_DESC-]
