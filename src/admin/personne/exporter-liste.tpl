<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://exporter-personnes.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="exporter.js"></script>
<style type="text/css">
<!--
td.cellule_sous_titre { text-align: left; }
-->
</style>
</head>
<body class="panneau">
<form action="exporter-liste.php" method="post" target="liste">
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;</td>
<td><img src="theme://onglet/onglet_tab-1x1.gif" border="0"></td>
<td class="onglet_tab_1x2">&nbsp;Liste&nbsp;à&nbsp;exporter&nbsp;</td>
<td><img src="theme://onglet/onglet_tab-1x3.gif" border="0"></td>
</tr>
</table>
</td>
<td class="cellule_sous_titre_vide" width="1%" nowrap="nowrap">&nbsp;<img src="theme://icones/ajouter-bas.gif" border="0">&nbsp;<a href="javascript: ajouter_personnes_liste();" onfocus="blur()">Ajouter</a>&nbsp;</td>
<td width="1%" nowrap="nowrap">&nbsp;|&nbsp;<img src="theme://icones/retirer-haut.gif" border="0">&nbsp;<a href="javascript: retirer_personnes_liste();" onfocus="blur()">Retirer</a>&nbsp;</td>
<td width="1%" nowrap="nowrap">&nbsp;|&nbsp;<a href="javascript: exporter_liste_personnes();" onfocus="blur()">Exporter</a>&nbsp;</td>
<td width="1%" nowrap="nowrap">&nbsp;|&nbsp;<a href="javascript: vider_liste_personnes();" onfocus="blur()">Vider</a>&nbsp;</td>
<td width="1%">&nbsp;</td>
</tr>
</table>
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td class="cellule_sous_titre_vide" width="1%"><img src="commun://espacer.gif" width="30" height="1"></td>
<td class="cellule_sous_titre" width="1%"><input type="checkbox" name="IDPERS" onfocus="blur()" onclick="select_deselect_checkbox(this)"></td>
<td class="cellule_sous_titre" width="33%">&nbsp;<a href="javascript: trier('nom','{nom->tri->ordre}');" onfocus="blur()">Nom</a>{nom->image->tri}&nbsp;</td>
<td class="cellule_sous_titre" width="33%">&nbsp;<a href="javascript: trier('prenom','{prenom->tri->ordre}');" onfocus="blur()">Pr&eacute;nom</a>{prenom->image->tri}&nbsp;</td>
<td class="cellule_sous_titre" width="33%">&nbsp;<a href="javascript: trier('pseudo','{pseudo->tri->ordre}');" onfocus="blur()">Pseudo</a>{pseudo->image->tri}&nbsp;</td>
</tr>
[BLOCK_PERSONNE+]
<tr>
<td class="numero_ligne" width="1%">{personne->position}</td>
<td width="1%"><input id="lettre_{id->lettre}" type="checkbox" name="IDPERS[]" value="{personne->id}" onclick="verif_checkbox(event)" onfocus="blur()"></td>
<td class="{td->nom->class}" width="33%">{personne->nom}</td>
<td class="{td->prenom->class}" width="33%">&nbsp;{personne->prenom}</td>
<td class="{td->pseudo->class}" width="33%">&nbsp;{personne->pseudo}</td>
</tr>
[BLOCK_PERSONNE-]
<tr><td colspan="4">&nbsp;</td></tr>
</table>
<input type="hidden" name="TRI" value="{TRI->value}">
<input type="hidden" name="ORDRE_TRI" value="{ORDRE_TRI->value}">
<input type="hidden" name="LISTE_IDPERS" value="{LISTE_IDPERS->value}">
</form>
</body>
</html>
[SET_IMAGE_TRI_ASC+]&nbsp;<img src="theme://sort-incr.gif" border="0">[SET_IMAGE_TRI_ASC-]
[SET_IMAGE_TRI_DESC+]&nbsp;<img src="theme://sort-desc.gif" border="0">[SET_IMAGE_TRI_DESC-]
