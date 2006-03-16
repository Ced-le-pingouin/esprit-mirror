<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript">
<!--
var win;

function init() {
	win = new DOMWindow();
	
	redimensionner();
	
	if (top.recuperer)
		top.recuperer();
}

function redimensionner() {
	var iHauteur = win.innerHeight();
	var elEditeur = new DOMElement("id_{editeur->nom}");
	var elVisualiseur = new DOMElement("idVisualiseur");
	var tmp = iHauteur-80;
	if (tmp < 35) tmp = 35;
	elEditeur.setHeight(tmp);
	tmp = iHauteur-20;
	if (tmp < 35) tmp = 35;
	elVisualiseur.setHeight(tmp);
}

function editeur() {
	if (top.oSousMenu) {
		top.oSousMenu().afficher_visualiser();
		document.getElementById("idVisualiseur").className = "cacher";
		document.getElementById("idEditeur").className = "afficher";
	}
	
	return false;
}

function visualiser() {
	
	if (top.oSousMenu) {
		with (document.forms[0]) {
			action = "editeur_visualiser.php";
			target = "visualiseur";
			submit();
		}
		
		top.oSousMenu().afficher_editer();
		
		document.getElementById("idEditeur").className = "cacher";
		document.getElementById("idVisualiseur").className = "afficher";
	}
	
	return false;
}
function insererBalise(v_sBaliseDepart,v_sBaliseFin) { insertAtCursor(document.forms[0].elements["{editeur->nom}"],v_sBaliseDepart,v_sBaliseFin); }
//-->
</script>
<style type="text/css">
<!--
.afficher, .cacher {position: absolute; left: 0px; top: 0px; margin: 5 5 5 5; }
.afficher { visibility: visible; display: block; }
.cacher { visibility: hidden; display: none; }
textarea.editeur_texte { width: 100%; height: 100%; }
-->
</style>
</head>
<body onload="init()" onresize="redimensionner()">
<form action="" method="post">
<div id="idEditeur" class="afficher">
[BLOCK_EDITEUR+][BLOCK_EDITEUR-]
</div>
<div id="idVisualiseur" class="cacher">
[BLOCK_VISUALISATEUR+][BLOCK_VISUALISATEUR-]
</div>
<input type="hidden" name="f" value="">
</form>
</body>
</html>

[SET_VISUALISEUR+]
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td><iframe id="id_visualiseur" name="visualiseur" src="" frameborder="0" width="100%" style="border-color: rgb(255,255,255); height: 420px;"></iframe></td></tr>
</table>
[SET_VISUALISEUR-]
