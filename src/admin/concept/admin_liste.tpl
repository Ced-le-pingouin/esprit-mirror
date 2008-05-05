<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://econcept.css">
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_oObj = null;
var g_oObjStyle = null;

function changerMenu(v_iType,v_iForm,v_iModule,v_iRubrique,v_iUnite,v_iActiv,v_iSousActiv)
{ // v.1.02
	top.frames['ADMINFRAMEMENU'].location = "admin_menu.php"
		+ "?type=" + v_iType
		+ "&params=" + v_iForm 
		+ ":" + v_iModule 
		+ ":" + v_iRubrique 
		+ ":" + v_iUnite 
		+ ":" + v_iActiv 
		+ ":" + v_iSousActiv;
}

function afficherIndice()
{
	if (typeof(arguments[0]) != "string" || !document.getElementById(arguments[0]))
		return;
	
	var iLeft = document.getElementById(arguments[0]).offsetLeft-20;
	var iTop = document.getElementById(arguments[0]).offsetTop-1;
		
	with (document.getElementById("id_editer").style)
	{
		left = iLeft;
		top = iTop;
		visibility = ((iLeft>0 && iTop>0) ? "visible" : "hidden");
	}
}

function changerStyle(v_oObj)
{
	if (g_oObj != null) {
		g_oObj.style.fontWeight = g_oObjStyle["fontWeight"];
		g_oObj.style.color = g_oObjStyle["textColor"];
	}
	
	g_oObj = v_oObj;
	g_oObjStyle = new Array();
	g_oObjStyle["fontWeight"] = g_oObj.style.fontWeight;
	g_oObjStyle["textColor"] = g_oObj.style.color;
	g_oObj.style.fontWeight = 'bolder';
	g_oObj.style.color = 'rgb(0,0,0)';
}

function init()
{
	var sIndice = null;
	
	// Mettre à jour le menu après chargement complète de cette page
	top.frames["ADMINFRAMEMODIF"].location = "admin_modif.php{url.params}";
	top.frames["ADMINFRAMEMENU"].location = "admin_menu.php{url.params}";
	
	if (document.getElementById && document.getElementById('{params.indice}'))
	{
		
		var oDomWin  = new DOMWindow(self);
		var oDomElem = new DOMElement("a.{params.indice}");
		
		var iScrollTop = oDomElem.getOffsetTop()-50;
		sIndice = oDomElem.element.parentNode.getAttribute("id");
		
		if (sIndice == null)
			sIndice = "id_formation";
		
		if (iScrollTop < 0)
			iScrollTop = 0;

		changerStyle(oDomElem.element);
		afficherIndice('{params.indice}');
		oDomWin.scrollTo(0,iScrollTop);
	}
}
//-->
</script>
</head>
<body class="econcept_liste" onload="init()">
[BLOCK_FORMATION+]
<div class="formation">
<span id="{formation.id}:0:0:0:0:0"><a id="a.{formation.id}:0:0:0:0:0" class="Formation" href="admin_modif.php?type={formation.type}&params={formation.id}:0:0:0:0:0" target="ADMINFRAMEMODIF" title="{formation.nom}" target="ADMINFRAMEMODIF" onclick="changerMenu({formation.type},{formation.id},0,0,0,0,0); afficherIndice('{formation.id}:0:0:0:0:0'); changerStyle(this);" onfocus="blur()">{formation.nom}</a></span>
[BLOCK_MODULE+]
<div class="module">
<span id="{formation.id}:{module.id}:0:0:0:0" class="module_intitule">&nbsp;{module.intitule}<a id="a.{formation.id}:{module.id}:0:0:0:0" href="admin_modif.php?type=2&params={formation.id}:{module.id}:0:0:0:0" target="ADMINFRAMEMODIF" title="{module.nom}" onclick="changerMenu({module.type},{formation.id},{module.id},0,0,0,0); afficherIndice('{formation.id}:{module.id}:0:0:0:0'); changerStyle(this);" onfocus="blur()">{module.nom}</a></span>
<hr class="module">
[BLOCK_RUBRIQUE+]
<div class="rubrique">
<span id="{formation.id}:{module.id}:{rubrique.id}:0:0:0" class="rubrique_intitule">{rubrique.intitule}<a id="a.{formation.id}:{module.id}:{rubrique.id}:0:0:0" href="admin_modif.php?type={rubrique.type}&params={formation.id}:{module.id}:{rubrique.id}:0:0:0" target="ADMINFRAMEMODIF" onclick="changerMenu({rubrique.type},{formation.id},{module.id},{rubrique.id},0,0,0); afficherIndice('{formation.id}:{module.id}:{rubrique.id}:0:0:0'); changerStyle(this);" onfocus="blur()" title="{rubrique.nom}">{rubrique.nom}</a>&nbsp;&nbsp;<span style="font-size: 7pt; font-weight: normal;">({rubrique.index})</span>
[BLOCK_ACTIVITE+]
<div class="activite">
<span id="{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:0">{activite.index}.&nbsp;&nbsp;<a id="a.{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:0" href="admin_modif.php?type={activite.type}&params={formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:0" target="ADMINFRAMEMODIF" title="{activite.nom}" onclick="changerMenu({activite.type},{formation.id},{module.id},{rubrique.id},0,{activite.id},0); afficherIndice('{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:0'); changerStyle(this);" onfocus="blur()">{activite.nom}</a></span>
<div class="sous_activite">
[BLOCK_SOUS_ACTIVITE+]<span id="{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:{sousactivite.id}">{sousactivite.index}.&nbsp;&nbsp;<a id="a.{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:{sousactivite.id}" href="admin_modif.php?type={sousactivite.type}&params={formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:{sousactivite.id}" target="ADMINFRAMEMODIF" title="{sousactivite.nom}" onclick="javascript: changerMenu({sousactivite.type},{formation.id},{module.id},{rubrique.id},0,{activite.id},{sousactivite.id}); afficherIndice('{formation.id}:{module.id}:{rubrique.id}:0:{activite.id}:{sousactivite.id}'); changerStyle(this);" onfocus="blur()">{sousactivite.nom}</a></span><br>[BLOCK_SOUS_ACTIVITE-]
</div>
</div>
[BLOCK_ACTIVITE-]
</div>
[BLOCK_RUBRIQUE-]
</div>
[BLOCK_MODULE-]
</div>
[BLOCK_FORMATION-]
[BLOCKELSE_FORMATION+]
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td><img src="theme://econcept/econcept.gif" border="0"></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td style="text-align: right;"><img src="theme://econcept/econcept-types.gif" border="0"></td></tr>
</table>
[BLOCKELSE_FORMATION-]
<div id="id_editer"><img src="images/editer.gif" width="16" height="16" border="0"></div>
<img src="commun://espacer.gif" alt="" width="100%" height="50" border="0">
</body>
</html>
