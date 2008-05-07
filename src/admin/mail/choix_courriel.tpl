<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
[BLOCK_HTML_HEAD+][BLOCK_HTML_HEAD-]
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_oWin;
var g_oElem;

function redimensionner()
{
	if (document.getElementById)
	{
		var oElem = new DOMElement("idChoisirDansListe");
		var iHauteur = parseInt(g_oWin.innerHeight()) - parseInt(oElem.getHeight()) - 60;
		if (iHauteur < 35) iHauteur = 35;
		g_oElem.setHeight(iHauteur);
	}
}

function valider()
{
	var oElems = self.frames[0].document.forms[0].elements;
	
	for (var i=0; i<oElems.length; i++)
		if (oElems[i].type == "checkbox" &&
			oElems[i].checked)
		{
			if (oElems[i].name.indexOf("idPers[]") != -1)
				document.forms[0].elements["idPers"].value += (document.forms[0].elements["idPers"].value.length > 0 ? "x" : "")
					+ oElems[i].value;
		}
	// Rafraîchir le menu
	top.rafraichir_menu('');
	
	// Envoyer le formulaire
	document.forms[0].submit();
}

function init()
{
	if (document.getElementById)
	{
		g_oWin = new DOMWindow(self);
		g_oElem = new DOMElement("idListeDestinataires");
		redimensionner();
	}
}
//-->
</script>
</head>
<body onload="init()" onresize="redimensionner()">
{form}
[BLOCK_ENVOYER_A+]
[VAR_TITRE+]Envoyer &agrave;&nbsp;:[VAR_TITRE-]
[VAR_TEXTE+]<iframe name="CHOIX_COURRIEL_LISTE" id="idListeDestinataires" src="{iframe.src}" border="0" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="310" scrolling="yes"></iframe>[VAR_TEXTE-]
[BLOCK_ENVOYER_A-]

[BLOCK_CHOISIR_BOITE_COURRIEL+]
[VAR_TITRE+]Choisir votre bo&icirc;te d'envoi&nbsp;:[VAR_TITRE-]
[VAR_TEXTE+]
<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr><td><input type="radio" name="boiteCourrielle" onfocus="blur()" value="{radio['plateforme'].value}" id="{radio['plateforme'].value}"></td><td><label for="{radio['plateforme'].value}">Celle de la plate-forme</label></td></tr>
[BLOCK_UTILISER_BOITE_COURRIELLE_PC+]<tr><td><input type="radio" name="boiteCourrielle" onfocus="blur()" value="{radio['os'].value}" id="{radio['os'].value}" checked="checked"></td><td><label for="{radio['os'].value}">Celle de votre ordinateur</label></td></tr>[BLOCK_UTILISER_BOITE_COURRIELLE_PC-]
<tr><td>&nbsp;</td></tr>
</table>
[VAR_TEXTE-]
[BLOCK_CHOISIR_BOITE_COURRIEL-]
<input type="hidden" name="typeCourriel" value="{type_courriel}">
<input type="hidden" name="idStatuts" value="">
<input type="hidden" name="idEquipes" value="">
<input type="hidden" name="idPers" value="">
{/form}
</body>
</html>
[SET_SEPARATEUR_BLOC+]<img src="commun://espacer.gif" height="15" width="100%" border="0">[SET_SEPARATEUR_BLOC-]
