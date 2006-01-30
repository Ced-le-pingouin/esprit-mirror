<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="dossier_formations_event.css">
<script type="text/javascript" language="javascript" src="dossier_formations_event.js"></script>
</head>
<body>
<form action="dossier_formations_event.php" method="get">
[BLOCK_MODIFIER_DOSSIER+]
<span class="label">Nom&nbsp;:</span><input id="idNomDossierForms" type="text" name="nomDossierForms" value="{dossier.nom}"><br>
<span class="label">Num&eacute;ro d'ordre&nbsp;:</span><select id="idOrdreDossierForms" name="ordreDossierForms">[BLOCK_NUMERO_ORDRE+]<option value="{dossier.numero_ordre}"{dossier.numero_ordre.selected}>{dossier.numero_ordre}</option>[BLOCK_NUMERO_ORDRE-]</select><br>
<span class="label">Premier dossier&nbsp;:</span><input type="checkbox" name="premierDossierForms"{dossier.premier.checked}><br>
[BLOCK_MODIFIER_DOSSIER-]
[BLOCK_SUPPRIMER_DOSSIER+]
<div id="suppression">
<p id="suppression_message">Vous êtes sur le point de supprimer le dossier<br>&laquo;&nbsp;<span id="titre_dossier">{dossier.nom}</span>&nbsp;&raquo;</p>
<p id="suppression_confirmer">Voulez-vous continuer&nbsp;?</p>
</div>
[BLOCK_SUPPRIMER_DOSSIER-]
<input type="hidden" name="idDossierForms" value="{dossier_formations.id}">
<input type="hidden" name="visibleDossierForms" value="on">
<input type="hidden" name="event" value="appliquer:{inputs.event.value}">
</form>
</body>
</html>

