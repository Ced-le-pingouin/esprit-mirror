<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript" src="javascript://tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
<script type="text/javascript" language="javascript">
<!--
var win;

initEditeur("exact", "id_{editeur->nom}", {tableau_de_bord->actif} );

function init() {
	win = new DOMWindow();
	redimensionner();
	if (top.recuperer) {
		tinyMCE.setContent(top.recuperer());
	}
}

function redimensionner() {
	var elEd = document.getElementById("mce_editor_0");
	var iHauteur = win.innerHeight();
	var tmp = iHauteur-110;
	if (tmp < 35) tmp = 35;
	elEd.style.height = tmp + 'px';
	// redimensionnement horizontal : pb de frames ?
	// ...
}

//-->
</script>
<style type="text/css">
<!--
textarea.editeur_texte { width: 100%; height: 100%; }
-->
</style>
</head>
<body onload="init()" onresize="redimensionner()">
<form action="" method="post">
<div id="idEditeur">
<textarea id="id_{editeur->nom}" name="{editeur->nom}" cols="80" rows="20" class="editeur_texte"></textarea>
</div>
<input type="hidden" name="f" value="">
</form>
</body>
</html>
