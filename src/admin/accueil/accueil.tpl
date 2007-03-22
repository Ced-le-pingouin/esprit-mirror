<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
div.hidden { display:none; }
h1 { font-size:18px; }
h2 { font-size:16px; }
div.valider { float:right; text-align:right; }
div.valider a { color:black; font-size:13px; border:1px solid black; background-color:#C0C0C0; padding:2px 3px; }
div.valider a:hover { background-color:#D0D0D0;}
div.valider span { color:red; font-style:italic; margin-right:1em;}
/*
form.liensForm label { display:block; margin:2px 0; }
form.liensForm label span { width:15ex; text-align:right; }
*/
form.liensForm input { width:70ex; }
form.liensForm button { margin-left:5ex; }
form.liensForm label { display:block; margin:2px 0; padding:0; }
form.liensForm label span {
  width:8ex; 
  display: -moz-inline-box;
  display: inline-block;
  text-align: left;
  margin: 0;
  padding: 0 1em 0 1em;
}

#breves ul { list-style-type: none; margin:0; padding:0; }
#brevesForm li { margin:4px 0; }
#brevesForm li span {
  float:left;
  width:51ex; 
  border: 1px solid gray;
  text-align: left;
  margin: 0;
}
input.date { margin-left:1ex; width:14ex; }
</style>
<script type="text/javascript" language="javascript" src="javascript://tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
<script type="text/javascript">

initEditeur("textareas", "", false );

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
	if ('{onglet}' != id)
		window.location = "{self}?onglet="+id;
/*
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
*/
}
function valider( id ) {
	var node = document.getElementById(id);
	var forms = node.getElementsByTagName('form');
	forms[0].submit();
}
function changed( id ) {
	var nodes = getElementsByClassName(document.getElementById(id),"div","valider");
	var spans = nodes[0].getElementsByTagName('span');
	spans[0].innerHTML = "Non enregistré";
}
</script>
</head>
<!-- <body onload="showOnly('{onglet}')"> -->
<body>

[BLOCK_AVERTISSEMENT+]
<div id="avertissement">
<div class="valider"><span></span><a href="#" onclick="valider('avertissement')">Valider</a></div>
<h1>Avertissement</h1>
<p><em>Texte placé sous la zone de login, sur la gauche de la page d'accueil.</em></p>
<form action="{self}" name="avertissement" method="post">
<input type="hidden" name="modifier" value="avertissement" />
<input type="hidden" name="onglet" value="avertissement" />
{avertissementEditeur}
</form>
</div>
[BLOCK_AVERTISSEMENT-]

[BLOCK_TEXTEACCUEIL+]
<div id="texteAccueil">
<div class="valider"><span></span><a href="#" onclick="valider('texteAccueil')">Valider</a></div>
<h1>Texte d'accueil</h1>
<form action="{self}" name="texteAccueil" method="post">
<input type="hidden" name="modifier" value="texteAccueil" />
<input type="hidden" name="onglet" value="texteAccueil" />
{texteAccueilEditeur}
</form>
</div>
[BLOCK_TEXTEACCUEIL-]

[BLOCK_LIENS+]
<div id="liens">
<h1>Liens</h1>
  <form name="liensTitre" id="liensTitre" action="{self}" method="post">
  <input type="hidden" name="modifier" value="titre" />
  <input type="hidden" name="titre" value="liens" />
  <input type="hidden" name="onglet" value="liens" />
  <label><span>Titre de la rubrique :</span><input type="text" name="texte" value="{liens->titre}" /></label>
  <button name="submit" value="submit" type="submit">Valider</button>
  </form>
[BLOCK_LOOP_LIENS+]
  <form name="liensForm{lien_id}" id="liensForm{lien_id}" class="liensForm" action="{self}" method="post">
  <input type="hidden" name="modifier" value="liens" />
  <input type="hidden" name="onglet" value="liens" />
  <input type="hidden" name="id" value="{lien_id}" />
  <p>
  <label><span>Titre :</span><input type="text" name="texte" value="{lien_text}" /></label>
  <label><span>Lien :</span><input type="text" name="lien" value="{lien_lien}" /></label>
  <label><span>Type :</span><select name="typeLien" size="1">
	<option{sel_actuelle} value="actuelle">page actuelle</option>
	<option{sel_nouvelle} value="nouvelle">nouvelle page</option>
	<option{sel_popup}>popup</option>
	<option{sel_inactif}>inactif</option>
  </select>
  </label>
  <label><span>Position :</span>{lien_position} sur {lien_positionTotal}
  <button name="delete" value="delete" type="submit">Supprimer</button>
  <button name="submit" value="submit" type="submit">Valider</button>
  </label>
  </p>
  </form>
[BLOCK_LOOP_LIENS-]
  <h2>Nouveau lien</h2>
  <form name="liensFormNew" id="liensFormNew" class="liensForm" action="{self}" method="post">
  <input type="hidden" name="modifier" value="liens" />
  <input type="hidden" name="onglet" value="liens" />
  <input type="hidden" name="id" value="0" />
  <p>
  <label><span>Titre :</span><input type="text" name="texte" value="" /></label>
  <label><span>Lien :</span><input type="text" name="lien" value="" /></label>
  <label><span>Type :</span><select name="typeLien" size="1">
	<option value="actuelle">page actuelle</option>
	<option value="nouvelle">nouvelle page</option>
	<option selected="1">popup</option>
	<option>inactif</option>
  </select>
   </label>
 <label><span>Position :</span>{lien_position}
  <button name="submit" value="submit" type="submit">Valider</button>
  </label>
  </p>
  </form>
</div>
[BLOCK_LIENS-]

[BLOCK_BREVES+]
<div id="breves">
<h1>Brèves</h1>
[BLOCK_BREVES_TITRE+]
  <form name="brevesTitre" id="brevesTitre" action="{self}" method="post">
  <input type="hidden" name="modifier" value="titre" />
  <input type="hidden" name="titre" value="breves" />
  <input type="hidden" name="onglet" value="breves" />
  <label><span>Titre de la rubrique :</span><input type="text" name="texte" value="{breves->titre}" /></label>
  <button name="submit" value="submit" type="submit">Valider</button>
  </form>
[BLOCK_BREVES_TITRE-]
  <form name="brevesForm" id="brevesForm" action="{self}" method="post">
  <input type="hidden" name="modifier" value="breves" />
  <input type="hidden" name="onglet" value="breves" />
  <ul>
[BLOCK_LOOP_BREVES+]
  <li>
  <span>{texteDebut}</span>
  <button name="selectBreve" value="{breve_id}" type="submit">Editer</button>
  <button name="hideBreve" value="{breve_id}" type="submit">Masquer</button>
  </li>
[BLOCK_LOOP_BREVES-]
[BLOCK_EDIT_BREVE+]
<div class="valider"><span></span><br /><br /><a href="#" onclick="valider('breves')">Valider</a></div>
  <button name="retour" value="breves" type="submit" style="position:absolute;top:2px;right:2px">Retour à la liste</button>
  <input name="editBreve" value="{breve_id}" type="hidden" />
<!--   <button name="editBreve" value="{breve_id}" type="submit">Valider</button><br /> -->
  <li><label><span>Date de début : <em>(yyyy-mm-dd ou vide)</em></span><input type="text" name="dateDeb" class="date" value="{breve_dateDeb}" onchange="changed('breves')" /></label></li>
  <li><label><span>Date de fin : <em>(yyyy-mm-dd ou vide)</em></span><input type="text" name="dateFin" class="date" value="{breve_dateFin}" onchange="changed('breves')" /></label></li>
  <li><label>Position : {breve_position} sur {breve_positionTotal}</label></li>
  <li>{brevesEditeur}</li> 
[BLOCK_EDIT_BREVE-]
  </ul>
  </form>
</div>
[BLOCK_BREVES-]

</body>
</html>

