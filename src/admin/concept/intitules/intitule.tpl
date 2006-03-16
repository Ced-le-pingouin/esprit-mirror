<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<script type="text/javascript" language="javascript">
<!--
function init()
{
{fonction->init}
}

function retIntitules()
{
	var amIntitules = new Array();
	var sListeIntitules = unescape(document.forms[0].elements["intitules"].value);
	
	amIntitules = sListeIntitules.split(";");
	
	return amIntitules;
}
//-->
</script>
<style type="text/css">
<!--
body { background-image: none; }
#idInfos {
	position: absolute;
	left: 15; top: 40;
	width: 200;
	background-color: rgb(206,109,14);
	border: rgb(79,47,24) solid 1px;
	visibility: hidden;
	text-align: center;
	padding: 10px;
}
-->
</style>
</head>
<body onload="init()">
<form>
<table border="0" cellspacing="1" cellpadding="3" width="100%">
<tr>
<td class="cellule_sous_titre"><img src="theme://" width="1" height="20" border="0"></td>
<td class="cellule_sous_titre"><div style="text-align: left;">&nbsp;Nom</div></td>
</tr>
[BLOCK_INTITULE+]
<tr>
<td{intitule->style->classe}>{gestion_intitule}</td>
<td width="99%"{intitule->style->classe}><b>{intitule->nom}</b></td>
</tr>
[BLOCK_INTITULE-]
</table>
<input type="hidden" name="intitules" value="{liste->intitules}">
</form>
<div id="idInfos">Vous ne pouvez pas effacer cet intitul&eacute;, car elle est utilis&eacute; autre part.<br><br><a href="javascript: document.getElementById('idInfos').style.visibility = 'hidden'; void(0);">Ok</a></div>
</body>
</html>
[SET_FOND_CELLULE_CLAIR+]class="cellule_clair"[SET_FOND_CELLULE_CLAIR-]
[SET_FOND_CELLULE_FONCE+]class="cellule_fonce"[SET_FOND_CELLULE_FONCE-]
[SET_MENU_MODIF+]<input type="radio" name="intitule" value="{intitule->nom}" onclick="top.oModif().modifier('{intitule->id}',this.value)" onfocus="blur()">[SET_MENU_MODIF-]
[SET_MENU_VIDE+]<img src="theme://espacer.gif" width="5" height="1" border="0">[SET_MENU_VIDE-]
