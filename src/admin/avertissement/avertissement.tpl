<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
div.hidden { display:none; }
h1 { font-size:18px; }
div.valider { float:right; }
div.valider a { color:black; font-size:13px; border:1px solid black; background-color:#C0C0C0; padding:2px 3px; }
div.valider a:hover { background-color:#D0D0D0;}
div.valider span { color:red; font-style:italic; margin-right:1em;}
</style>
<script type="text/javascript">
function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];		
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}	
	}
	return (arrReturnElements)
}
function showOnly( id ) {
	var nodes = getElementsByClassName(document,"div","hidden");
	for(var i=0; i<nodes.length; i++) {
		if (nodes[i].id !== id) {
			nodes[i].style.display = 'none';
		}
	}
	var e = document.getElementById(id);
	if (!e.style.display || e.style.display == 'none') {
		e.style.display = 'block';
	} else {
		e.style.display = 'none';
	}
}
function valider( id ) {
	var node = document.getElementById(id);
	var forms = node.getElementsByTagName('form');
	forms[0].submit();
}
function changed( id ) {
	var nodes = getElementsByClassName(document.getElementById(id),"div","valider");
	var spans = nodes[0].getElementsByTagName('span');
	spans[0].innerHTML = "Non sauvegardé";
}
</script>
</head>
<body onload="showOnly('{onglet}')">
<div id="avertissement" class="hidden">
<div class="valider"><span></span><a href="#" onclick="valider('avertissement')">Valider</a></div>
<h1>Avertissement</h1>
<p><em>Texte placé sous la zone de login, sur la gauche de la page d'accueil.</em></p>
{avertissement}
</div>

<div id="texteAccueil" class="hidden">
<div class="valider"><span></span><a href="#" onclick="valider('texteAccueil')">Valider</a></div>
<h1>Texte d'accueil</h1>
{texteAccueil}
</div>

<div id="liens" class="hidden">
<h1>Liens</h1>
{liens}
</div>

<div id="breves" class="hidden">
<h1>Brèves</h1>
{breves}
</div>
</body>
</html>

