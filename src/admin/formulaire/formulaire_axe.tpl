<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gestion des Axes/Tendances</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<style type="text/css">
html
{
	height: 100%;
	/* \*/ overflow: hidden; /**/
}
body
{
	background-color: rgb(253,249,238);
	margin: 0;
	padding: 0;
	color: rgb(0,0,0);
	font-family: Verdana,Tahoma,Arial,sans-serif;
	font-size: 12px;
	height: 100%;
	width: 100%;
	overflow: hidden;
}
#principal
{
	height: 90%;
	overflow: auto;
	margin: 0;
	padding: 7px;
}
#piedpage
{
	height: 10%;
	text-align: center;
	margin: 0;
	padding: 0 10px;
	border-top: solid black 1px;
	background-color: rgb(174,165,138);
	vertical-align: center;
}
#valider
{
	float: left;
	margin-top: 5px;
	margin-left: 5px;
	margin-right: 5px;
}
#fermer
{
	float: right;
	margin-top: 5px;
	margin-left: 5px;
	margin-right: 5px;
}
#gestion
{
	display: block;
	margin-top: 5px;
}
legend
{
	font-weight: bold;
}
a:link, a:visited, a:hover
{
	color: rgb(255,255,255);
	font-weight: bold;
}
</style>
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
<body class="popup">
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
	<a href="javascript: self.close();" id="fermer">Fermer</a>
	[BLOCK_LIEN+]
	<a href="javascript: appliquer();" id="valider">Valider</a>
	<a href="#" onclick="gestionaxes();" id="gestion">Gestion des axes (ajout/suppression)</a>
	[BLOCK_LIEN-]
</div>

</body>
</html>
