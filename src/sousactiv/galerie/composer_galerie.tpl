<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://dialogue.css">
<link type="text/css" rel="stylesheet" href="theme://composer_galerie.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="composer_galerie.js"></script>
</head>
<body>
<h1>[TXT_GALERIE_TITRE]&nbsp;<small>&raquo;&nbsp;{sousactiv.nom}</small></h1>
<p>[TXT_COMPOSER_SA_GALERIE_CONSIGNE]</p>
<form action="composer_galerie.php" method="post" target="Principale">
<table id="liste_ressources">
<thead>
<tr>
<th class="checkbox"><input type="checkbox" name="ressources" value="0"{ressource.checked}></th>
<th>[TXT_TITRE]</th>
<th>[TXT_ETAT]</th>
<th>[TXT_DEPOSE_PAR]</th>
</tr>
</thead>
[BLOCK_RESSOURCE+][BLOCK_RESSOURCE-]
</table>
<input type="hidden" name="action" value="">
<input type="hidden" name="personne" value="{personne.value}">
<input type="hidden" name="document" value="{document.value}">
<input type="hidden" name="collecticiel" value="{collecticiel.value}">
<input type="hidden" name="idsres" value="{idsres.value}">
<input type="hidden" name="idSA" value="{sousactiv.id}">
</form>
</body>
</html>

[SET_COLLECTICIEL+]
<tr>
<td colspan="4" class="collecticiel"><strong>{collecticiel.nom}</strong></td>
</tr>
[SET_COLLECTICIEL-]

[SET_RESSOURCE+]
<tr>
<td><input type="checkbox" name="ressources[]" value="{ressource.id}"{ressource.checked}></td>
<td class="document">{ressource.nom}</td>
<td class="etat">&nbsp;{ressource.etat}&nbsp;</td>
<td class="auteur">&nbsp;{ressource.auteur}&nbsp;</td>
</tr>
[SET_RESSOURCE-]

