<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript" src="javascript://tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
<script type="text/javascript" language="javascript">
<!--

initEditeur("exact", "id_{editeur->nom}", {tableau_de_bord->actif} );

function mySetContent(editor_id, body, doc) {
	if (top.recuperer) {
		body.innerHTML = top.recuperer();
	}
	return "";
}

function redimensionner() {
	var elEd = document.getElementById("id_{editeur->nom}");
	elEd.style.height = '100%';
	elEd.style.width = '100%';
}

//-->
</script>
<style type="text/css">
<!--
textarea.editeur_texte { width: 100%; height: 100%; }
-->
</style>
</head>
<body onload="redimensionner()" onresize="redimensionner()">
<form action="" method="post">
<div id="idEditeur">
<textarea id="id_{editeur->nom}" name="{editeur->nom}" cols="80" rows="20" class="editeur_texte"></textarea>
</div>
<input type="hidden" name="f" value="">
</form>
</body>
</html>
