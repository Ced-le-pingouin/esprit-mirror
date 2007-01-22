<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Ajouter un élément</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire_modif_ajout.css" />
<script src="selectionobj.js" type="text/javascript"></script>
</head>
<body {onload}>
<div id="contenu">
	<form action="formulaire_modif_ajout.php" name="formajout" method ="get">
	<select name="idtypeobj">
	[BLOCK_MODIF_AJOUT+]
		<option value="{id_type_obj}">{desc_type_obj}</option>
	[BLOCK_MODIF_AJOUT-]
	</select>
	<input type="hidden" name="idformulaire" value="{id_formulaire}" />
	<input type="hidden" name="bMesForms" value="{bMesForms}" />
	<input type="hidden" value="ajouter" name="ajouter" />
	</form>
</div>
<div id="barreaction">
	<a id="valider" href="#" onclick="document.forms['formajout'].submit();">Valider</a>
	<a id="fermer" href="#" onclick="window.close ();">Fermer</a>
</div>
</body>
</html>
