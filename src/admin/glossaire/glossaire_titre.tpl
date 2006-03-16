<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://glossaire/glossaire_composer.css">
<script type="text/javascript" language="javascript">
<!--
function init() {
	top.oMenu().location = "glossaire_titre-menu.php{menu}";
}

function annuler() {
	top.close();
}

function envoyer() {
	document.forms[0].submit();
}
//-->
</script>
</head>
<body class="glossaire_titre" onload="init()">
<form action="glossaire_titre.php" method="get">
[BLOCK_GLOSSAIRE_TITRE+][BLOCK_GLOSSAIRE_TITRE-]
<input type="hidden" name="glossaire_id" value="{glossaire->id}">
</form>
</body>
</html>
[SET_GLOSSAIRE_TITRE+]<input type="text" name="glossaire_titre" style="width: 100%;" value="{glossaire->titre}">[SET_GLOSSAIRE_TITRE-]