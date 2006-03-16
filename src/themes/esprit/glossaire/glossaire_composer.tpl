<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title></title>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://glossaire/glossaire_composer.css">
<script type="text/javascript" language="javascript" src="commun://glossaire/glossaire.js"></script>
</head>
<body class="gauche">
<h1 style="text-align: center;">{glossaire->titre}<br><br></h1>
<form>
[BLOCK_ELEMENTS_GLOSSAIRE+][BLOCK_ELEMENTS_GLOSSAIRE-]
<input type="hidden" name="idGlossaire" value="{glossaire->id}">
</form>
</body>
</html>
[SET_ELEMENT_GLOSSAIRE+]
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr>
<td colspan="1" rowspan="2"><input type="checkbox" name="idElementsGlossaire[]" value="{glossaire->element->id}"{glossaire->element->selectionne}></td>
<td><b>{glossaire->element->titre}</b></td>
</tr>
<tr><td>{glossaire->element->texte}</td></tr>
<tr><td colspan="2" style="text-align: right;">Ajouter&nbsp;|&nbsp;Modifier&nbsp;|&nbsp;Supprimer</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
[SET_ELEMENT_GLOSSAIRE-]