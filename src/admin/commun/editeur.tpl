<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script language="javascript" type="text/javascript" src="javascript://tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	theme : "advanced",
	plugins : "table,save,advhr,advlink,emotions,insertdatetime,preview,zoom,searchreplace,contextmenu",
// theme_advanced_buttons1_add_before : "save,separator",
theme_advanced_buttons1_add : "fontselect,fontsizeselect",
theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
theme_advanced_buttons3_add_before : "tablecontrols,separator",
theme_advanced_buttons3_add : "emotions,iespell,advhr,separator,print",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_path_location : "bottom",
plugin_insertdate_dateFormat : "%Y-%m-%d",
plugin_insertdate_timeFormat : "%H:%M:%S",
extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	mode : "exact",
	elements : "id_{editeur->nom}",
	language : "fr"
});
</script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript">
<!--
var win;

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
<!-- 
<table>
[BLOCK_TABLEAU_DE_BORD+]
<td><a href="javascript: tableau_de_bord('/i')" onfocus="blur()" title="Lien vers le tableau de bord individuel"><img src="commun://icones/24x24/tableaubord.gif"></a></td>
<td><a href="javascript: tableau_de_bord('/e')" onfocus="blur()" title="Lien vers le tableau de bord par Ã©quipe"><img src="commun://icones/24x24/tableaubord.gif"></a></td>
[BLOCK_TABLEAU_DE_BORD-]
</table>
 -->
