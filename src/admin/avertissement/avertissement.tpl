<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
div.hidden { display:none; }
h1 { font-size:18px; }
h2 { font-size:16px; }
div.valider { float:right; }
div.valider a { color:black; font-size:13px; border:1px solid black; background-color:#C0C0C0; padding:2px 3px; }
div.valider a:hover { background-color:#D0D0D0;}
div.valider span { color:red; font-style:italic; margin-right:1em;}
form.liensForm { width:85ex; }
form.liensForm label { display:block; margin:2px 0; }
form.liensForm label span { width:15ex; text-align:right; }
form.liensForm input { width:70ex; margin-left:1ex; }
form.liensForm button { margin-left:5ex; }
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
[BLOCK_LOOP_LIENS+]
  <form name="liensForm{lien_id}" id="liensForm{lien_id}" class="liensForm" action="" method="post">
  <input type="hidden" name="modifier" value="liens" />
  <input type="hidden" name="onglet" value="liens" />
  <p>
  <input type="hidden" name="id" value="{lien_id}" />
  <label><span>Titre :</span><input type="text" name="texte" value="{lien_text}" /></label>
  <label><span>Lien :</span><input type="text" name="lien" value="{lien_lien}" /></label>
  <label><span>Type :</span>
  <select name="typeLien" size="1">
	<option{sel_frame}>frame</option>
	<option{sel_page}>page</option>
	<option{sel_popup}>popup</option>
	<option{sel_inactif}>inactif</option>
  </select>
  <button name="submit" value="submit" type="submit">Valider</button>
  </p>
  </form>
[BLOCK_LOOP_LIENS-]
  <h2>Nouveau lien</h2>
  <form name="liensFormNew" id="liensFormNew" class="liensForm" action="" method="post">
  <input type="hidden" name="modifier" value="liens" />
  <input type="hidden" name="onglet" value="liens" />
  <p>
  <input type="hidden" name="id" value="0" />
  <label><span>Titre :</span><input type="text" name="texte" value="" /></label>
  <label><span>Lien :</span><input type="text" name="lien" value="" /></label>
  <label><span>Type :</span>
  <select name="typeLien" size="1">
	<option>frame</option>
	<option>page</option>
	<option selected="1">popup</option>
	<option>inactif</option>
  </select>
  <button name="submit" value="submit" type="submit">Valider</button>
  </p>
  </form>
</div>

<div id="breves" class="hidden">
<h1>Brèves</h1>
{breves}
</div>
</body>
</html>

