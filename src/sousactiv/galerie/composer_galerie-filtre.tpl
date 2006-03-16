<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://barre_filtres.css">
<script type="text/javascript" language="javascript" src="composer_galerie-filtre.js"></script>
</head>
<body>
<div id="barre_filtres">
<form action="composer_galerie.php" method="post" target="Principale">
[BLOCK_COLLECTICIELS+]
<select name="collecticiel">
<option value="0">Tous les collecticiels</option>
[BLOCK_COLLECTICIEL+]<option value="{collecticiel.id}">{collecticiel.nom}</option>[BLOCK_COLLECTICIEL-]
</select>
[BLOCK_COLLECTICIELS-]
<select name="document">
[BLOCK_STATUT+]<option value="{statut.id}">{statut.nom}</option>[BLOCK_STATUT-]
</select>
<select name="personne">
<option value="0">Tous les étudiants</option>
[BLOCK_PERSONNE+]<option value="{personne.id}">{personne.nom}</option>[BLOCK_PERSONNE-]
</select>
<input type="hidden" name="idSA" value="{sousactiv.id}">
</form>
</div>
</body>
</html>

