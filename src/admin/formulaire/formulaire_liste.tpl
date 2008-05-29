<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css" />
<style type="text/css">
<!--
form
{
	margin-bottom: 10px;
	margin-left: {sLargeur};
	margin-right: {sLargeur};
	margin-top: 10px;
}
.InterER
{
	margin-top: {iInterEnonRep}px;
}
.InterObj
{
	margin-top: {iInterElem}px;
}
-->
</style>
<script type="text/javascript" src="selectionobj.js"></script>
<script type="text/javascript">
function allerAPos(v_iNpos)
{
	if(v_iNpos)
	{
		document.location = '#' + v_iNpos;
	}
	else
	{
		idObj = retParamUrl(window.location,'idobj');
		if (idObj != null)
			document.location = '#' + idObj;
	}
}
</script>
<title>Modification des activités en ligne</title>
</head>
<body {onload} class="liste">
[BLOCK_INTRO+]
<div id="titrepagevierge">
	<img src="../../images/doc-plein.gif" alt="logo" />
	La conception d'activités totalement en ligne
	<span id="ute">Unit&eacute; de Technologie de l'&Eacute;ducation</span>
</div>
[BLOCK_INTRO-]
[BLOCK_FORMULAIRE+]
<form name="selection" class="formFormulaire" action="">
{sSelectModifTitre}
<table {sEncadrer} align="center" class="titre">
<tr>
	<td>
		{sTitre}
	</td>
</tr>
</table>
{ListeObjetFormul}
</form>
[BLOCK_FORMULAIRE-]
</body>
</html>
