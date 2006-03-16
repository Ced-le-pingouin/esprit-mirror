<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="dossier_formations.css">
<script type="text/javascript" language="javascript" src="dossier_formations.js"></script>
</head>
<body>
<h1 id="titre">[TITRE_CREER_MODIFIER_DOSSIER][BLOCK_NOM_DOSSIER+]<span>&nbsp;&raquo;&nbsp;{dossier.nom}</span>[BLOCK_NOM_DOSSIER-]</h1>
<p>S&eacute;lectionnez les formations que vous voulez placer dans ce dossier. Vous pouvez &eacute;galement indiquer un num&eacute;ro d'ordre pour chacune d'entre elles. Cliquez ensuite sur le lien &laquo;&nbsp;Enregistrer les modifications&nbsp;&raquo; pour valider les changements.</p>
<form action="dossier_formations.php" method="get">
<div id="liste_formations" class="liste_formations">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
<td>&nbsp;</td>
<td class="cellule_sous_titre">&nbsp;Ordre&nbsp;</td>
<td class="cellule_sous_titre" style="width: 99%;">&nbsp;Formation&nbsp;</td>
</tr>
[BLOCK_FORMATION+]
<tr>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><input type="checkbox" name="idForms[]" value="{formation.id}"{input.attributes}></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><select name="ordreForms[{formation.id}]"{select.attributes}>[OPTION_FORMATION_ORDRE+]<option{option.attributes}>{formation.ordre}</option>[OPTION_FORMATION_ORDRE-]</select></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]">{formation.nom}</td>
</tr>
[BLOCK_FORMATION-]
</table>
<input type="hidden" name="idDossierForms" value="{dossier_formations.id}">
<input type="hidden" name="event" value="sauver">
<input type="hidden" name="action" value="{inputs.action.value}">
</div>
</form>
</body>
</html>

