<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://exporter-personnes.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="exporter.js"></script>
</head>
<body>
<form action="exporter-personnes.php" method="get">
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
<td class="{td->nom->class}" width="33%">&nbsp;{personne->nom}</td>
<td class="{td->prenom->class}" width="33%">&nbsp;{personne->prenom}</td>
<td class="{td->pseudo->class}" width="33%">&nbsp;{personne->pseudo}</td>
</tr>
[BLOCK_PERSONNE-]
<tr><td colspan="4">&nbsp;</td></tr>
</table>
<input type="hidden" name="ID_FORM" value="{form->id_form}">
<input type="hidden" name="ID_STATUT" value="{form->id_statut}">
<input type="hidden" name="TRI" value="">
<input type="hidden" name="ORDRE_TRI" value="">
</form>
</body>
</html>
[SET_IMAGE_TRI_ASC+]&nbsp;<img src="theme://sort-incr.gif" border="0">[SET_IMAGE_TRI_ASC-]
[SET_IMAGE_TRI_DESC+]&nbsp;<img src="theme://sort-desc.gif" border="0">[SET_IMAGE_TRI_DESC-]
