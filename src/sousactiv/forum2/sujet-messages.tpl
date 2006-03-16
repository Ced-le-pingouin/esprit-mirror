<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://forum/sujet-messages.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript">
<!--
// [0] => identifiant du radio et [1] => identifiant de l'élément
var g_amMessageSelect = new Array(2);
function init()
{
	top.oFrmMessagesMenu().location.replace("messages-menu.php?idSujet={sujet->id}&idEquipe={equipe->id}&nbMessages={nombre_messages}");
	
	if (top.page_message && top.page_message[0] == ret_id_sujet())
	{
		var oDOMWin = new DOMWindow();
		oDOMWin.scrollTo(top.page_message[1],top.page_message[2]);
	}
	else
	{
		top.page_message = new Array(ret_id_sujet(),0,0);
	}
}
function ret_id_sujet() { return document.forms[0].elements["idSujet"].value; }
function deselect_message()
{
	// Deseclectionner
	if (document.getElementById && g_amMessageSelect[1] != null)
	{
		var aoElems = document.forms[0].elements['idMessage'];
		if (typeof(aoElems.length) == "undefined")
			aoElems.checked = false;
		else
			for (var i=0; i<aoElems.length; i++) aoElems[i].checked = false;
		document.getElementById(g_amMessageSelect[1]).className = "message";
	}
	g_amMessageSelect = new Array(2);
}
function select_deselect_message(v_sElem)
{ // v1.1
	// Deseclectionner
	if (document.getElementById && g_amMessageSelect[1] != null)
		document.getElementById(g_amMessageSelect[1]).className = "message";
	
	// Sauvegarder les modifications
	g_amMessageSelect[0] = select_deselect_radio(document.forms[0].elements['idMessage'],g_amMessageSelect[0]);
	g_amMessageSelect[1] = v_sElem;
	
	// Selectionner
	if (document.getElementById)
		document.getElementById(v_sElem).className = (g_amMessageSelect[0] != null ? "message_selectionne" : "message");
}
//-->
</script>
</head>
<body onload="init()">
<form action="{form->action}" method="get">
<table border="0" cellspacing="0" cellpadding="2" width="100%" style="background-color: rgb(255,255,255); border: rgb(230,230,230) solid 1px;">
<tr><td>&nbsp;</td></tr>
[BLOCK_MESSAGE+][BLOCK_MESSAGE-]
</table>
<input type="hidden" name="idSujet" value="{sujet->id}">
</form>
</body>
</html>

[SET_MESSAGE+]
<tr>
<td>&nbsp;</td>
<td><img src="theme://espacer.gif" width="1" height="1" border="0"></td>
<td width="99%">
<table id="id_message_{message->id}" border="0" cellspacing="0" cellpadding="2" width="100%" class="message">
<tr>
<td class="message_infos_auteur_alias">{personne->sexe}</td>
<td class="message_infos_auteur">&nbsp;{personne->email}<img src="commun://espacer.gif" width="30" height="1" border="0"><span class="nb_messages_deposes_personne">{personne->nb_messages_deposes}&nbsp;message(s)</span>&nbsp;</td>
<td class="message_infos_date"><img src="theme://espacer.gif" width="150" height="1"><br>{message->date}</td>
</tr>
<tr><td class="message_texte" colspan="3">{message->texte}{message->ressources}</td></tr>
</table>
</td>
<td valign="bottom">{message->bouton_selection}</td>
</tr>
<tr><td colspan="2">&nbsp;</td><td>{ligne_separation_sujets}&nbsp;</td><td>&nbsp;</td></tr>
[SET_MESSAGE-]

[SET_EMAIL+]<a href="javascript: void(0);" onclick="choix_courriel('{a.choix_courriel.href}'); return false;" onfocus="blur()" title="Cliquer ici pour envoyer un courriel">{personne->pseudo}</a>[SET_EMAIL-]
[SET_SANS_EMAIL+]<span class="sans_email" title="Pas dadresse email disponible">{personne->pseudo}</span>[SET_SANS_EMAIL-]

[SET_SELECTIONNER_MESSAGE+]<input type="radio" name="idMessage" value="{message->id}" onclick="select_deselect_message('id_message_{message->id}')" onfocus="blur()">[SET_SELECTIONNER_MESSAGE-]

[SET_LIGNE_SEPARATION_SUJETS+][SET_LIGNE_SEPARATION_SUJETS-]

[SET_IMAGE_HOMME+]<span title="{personne->nom_complet}" style="cursor: help;"><img src="theme://icones/boy.gif" width="14" height="26" border="0" hspace="0"></span>[SET_IMAGE_HOMME-]

[SET_IMAGE_FEMME+]<span title="{personne->nom_complet}" style="cursor: help;"><img src="theme://icones/girl.gif" width="14" height="26" border="0" hspace="0" align="left"></span>[SET_IMAGE_FEMME-]

[SET_FICHIER_ATTACHE+]<img src="commun://espacer.gif" width="100%" height="20" border="0"><div style="border: rgb(230,238,243) none 1px; padding: 3px; border-top-style: dashed; text-align: right;"><img src="commun://icones/12x12/disquette.gif" width="12" height="12" border="0">&nbsp;<a href="{a['fichier_attache']->href}" title="Cliquer ici pour télécharger le fichier" onfocus="blur()" target="_parent">{a['fichier_attache']->text}</a></div>[SET_FICHIER_ATTACHE-]

