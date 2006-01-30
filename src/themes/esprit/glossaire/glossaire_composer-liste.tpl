<html>
<head>
<title></title>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://dialog.css">
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://glossaire.js"></script>
</head>
<body class="gauche">
<form>
[BLOCK_LISTE_GLOSSAIRES+][BLOCK_LISTE_GLOSSAIRES-]
</form>
</body>
</html>
[SET_MENU_LIEN_ACTIF+]
<div style="text-align: left;"><input type="radio" name="ID_GLOSSAIRE" value="{glossaire->id}">&nbsp;&nbsp;<a href="glossaire_composer.php?idGlossaire={glossaire->id}" target="Principale" onfocus="blur()">{glossaire->titre}</a></div>
[SET_MENU_LIEN_ACTIF-]