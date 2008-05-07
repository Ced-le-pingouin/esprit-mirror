<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gestion des Axes/Tendances</title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css" />
<script type="text/javascript" language="javascript" src="{chemin_windows.js}"></script>
<script type="text/javascript">
<!--
function gestionaxes()
{
	PopupCenterOffset('gestion_axes.php?idformulaire={id_formulaire}','WinGestionAxes',550,400,'location=no,status=no,toolbar=no,scrollbars=yes',20,20);
}
function appliquer()
{
	window.document.forms['formaxe'].submit();
}
//-->
</script>
</head>
<body class="formulaire_axe">
<div id="principal">
[BLOCK_CHOIX+]
<form name="formaxe" action="formulaire_axe.php" method="post">
	<fieldset>
		<legend>Sélection des "axes/tendances" applicables à l'activité en ligne</legend>
		[BLOCK_AXES+]
		<input type="checkbox" name="axes[]" value="{id_axe}" {chk} />
		{couleur_police1}{desc_axe}{couleur_police2}<br />
		[BLOCK_AXES-]
		<input type="hidden" name="idformulaire" value="{id_formulaire}" />
		<input type="hidden" value="valider" name="valider" />
	</fieldset>
	</form>
	<br />
	<div align="center">
	Les axes présents actuellement dans l'activité en ligne sont écrits en <i>italique</i>
	</div>
[BLOCK_CHOIX-]
[BLOCK_CONFIRM+]
	<fieldset><legend>"Axes/tendances" appliqué à l'activité en ligne</legend>
	<div align="center">
	[BLOCK_AXES+]
	{desc_axe}<br />
	[BLOCK_AXES-]
	</div>
	</fieldset>
[BLOCK_CONFIRM-]
</div>
<div id="piedpage">
	<a href="javascript: self.close();" id="fermer_global">Fermer</a>
	[BLOCK_LIEN+]
	<a href="javascript: appliquer();" id="valider_global">Valider</a>
	<a href="#" onclick="gestionaxes();" id="gestion">Gestion des axes (ajout/suppression)</a>
	[BLOCK_LIEN-]
</div>

</body>
</html>
