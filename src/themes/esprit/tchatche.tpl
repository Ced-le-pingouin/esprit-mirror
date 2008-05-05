<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title></title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://chat.css">
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript">
<!--

var g_aListeChats = new Array({chats->liste});

var aoFenetresArchives = new Array();
var aoFenetresChats = new Array();

function recharger_liste_connectes() {
	top.oConnectes().location = top.oConnectes().location;
}

function tchatche(v_iIdChat,v_iIdEquipe,v_bConversationPrivee)
{
	var sUrl = "{chat->url}"
		+ "?idNiveau={niveau->id}"
		+ "&typeNiveau={niveau->type}"
		+ "&idChat=" + v_iIdChat
		+ "&idEquipe=" + v_iIdEquipe;
	var sNom = "winTchatche" + v_iIdChat + "x" + v_iIdEquipe;
	var iHauteur = (v_bConversationPrivee == '1' ? 462 : 313);
	var sCaracteristiques = ",resizable=yes,"
		+ "scrollbars=no,"
		+ "status=no";
	var oWinTchatche = PopupCenter(sUrl,sNom,602,iHauteur,sCaracteristiques);
	oWinTchatche.focus();
}

function ajouterFenetre(v_oFenetre,v_aoFenetres)
{
	var aoTmp = v_aoFenetres;
	var iIdx = 0;
	
	v_aoFenetres = new Array();
	
	for (i=0; i<aoTmp.length; i++)
		if (aoTmp[i] != v_oFenetre)
			v_aoFenetres[iIdx++] = aoTmp[i];
	
	v_aoFenetres[iIdx] = v_oFenetre;
	
	return v_aoFenetres;
}

function retListePlugins()
{
	for (i=0; i<navigator.plugins.length; i++)
		if (navigator.plugins[i].description &&
			navigator.plugins[i].description.search(/^.*java.*plug.?in.*1\.3.*$/i) != -1)
			alert(navigator.plugins[i].name + "\n"
				+ navigator.plugins[i].description + "\n"
				+ navigator.plugins[i].filename + "\n");
}

function init() {
	setInterval("recharger_liste_connectes()",5000);
}

function uninit()
{
	// Fermer toutes les fenêtres des chats
	for (i=0; i<aoFenetresChats.length; i++)
		if (!aoFenetresChats[i].closed)
			aoFenetresChats[i].close();
	
	// Fermer toutes les fenêtres des archives
	for (i=0; i<aoFenetresArchives.length; i++)
		if (!aoFenetresArchives[i].closed)
			aoFenetresArchives[i].close();
}

//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="1" cellpadding="3" width="100%">
[BLOCK_LISTE_CHATS+][BLOCK_LISTE_CHATS-]
</table>
</body>
</html>

[SET_PAS_CHAT_TROUVE+]<tr><td align="center"><img src="commun://espacer.gif" width="100%" height="50" border="0"><div class="attention" style="width: 70%;">Aucun chat n'a été créé dans cet espace</div></td></tr>[SET_PAS_CHAT_TROUVE-]

[SET_CHAT_ACTIF+]
<tr><td><a class="acceder_salon" href="javascript: tchatche('{chat->id}','{equipe->id}','{chat->salon_prive}');" title="Cliquer ici pour acc&eacute;der au salon" onfocus="blur()">{chat->nom}</a></td><td class="mode_salon">&nbsp;{chat->modalite}&nbsp;</td></tr><tr><td class="cellule_clair" colspan="2" align="center"><span id="id_liste_connectes{chat->id}_{equipe->id}">Pas d'utilisateur connect&eacute;</span></td></tr><tr><td class="acceder">&nbsp;<a href="javascript: tchatche('{chat->id}','{equipe->id}','{chat->salon_prive}');" title="Cliquer ici pour acc&eacute;der au salon" onfocus="blur()">Acc&eacute;der</a></td><td class="archives">{archive}&nbsp;</td></tr>
[SET_CHAT_ACTIF-]

[SET_CHAT_PASSIF+]
<tr><td><a class="acceder_salon">{chat->nom}</a></td><td class="mode_salon">&nbsp;{chat->modalite}&nbsp;</td></tr><tr><td class="cellule_clair" colspan="2" align="center"><span id="id_liste_connectes{chat->id}_{equipe->id}">Pas d'utilisateur connect&eacute;</span></td></tr><tr><td class="acceder">&nbsp;<a style="color: rgb(210,210,210)">Acc&eacute;der</a></td><td class="archives">{archive}&nbsp;</td></tr>
[SET_CHAT_PASSIF-]

[SET_ARCHIVE+]<span id="id_nombre_archives{chat->id}_{equipe->id}" class="archives">{archives->total}</span>&nbsp;archive(s)[SET_ARCHIVE-]

[SET_SEPARATEUR_CHATS+]
<tr><td colspan="2">&nbsp;</td></tr>
[SET_SEPARATEUR_CHATS-]
