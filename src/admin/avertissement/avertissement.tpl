<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
div.hidden { display:none; }
h1 { font-size:18px; }
h1:hover { text-decoration:underline; }
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
</script>
</head>
<body>
<h1 onclick="showOnly('avertissement')">Avertissement</h1>
<div id="avertissement" class="hidden">
<p><em>Texte placé sous la zone de login, sur la gauche de la page d'accueil.</em></p>
{avertissement}
</div>

<h1 onclick="showOnly('texteAccueil')">Texte d'accueil</h1>
<div id="texteAccueil" class="hidden">
{texteAccueil}
</div>

<h1 onclick="showOnly('liens')">Liens</h1>
<div id="liens" class="hidden">
{liens}
</div>

<h1 onclick="showOnly('breves')">Brèves</h1>
<div id="breves" class="hidden">
{breves}
</div>
</body>
</html>

