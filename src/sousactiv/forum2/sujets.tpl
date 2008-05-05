<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/forum.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="forum.js"></script>
<script type="text/javascript" language="javascript">
<!--
var idAncienNoeud = null;
var g_iDernierRadioSelect = null;
function init()
{
	if (typeof(top.g_iPageYOffsetSujets) == "undefined")
		top.g_iPageYOffsetSujets = 0;
	var oDOMWindow = new DOMWindow(self);
	oDOMWindow.scrollTo(0,top.g_iPageYOffsetSujets);
	rafraichir_infos_sujet(); 
}
function envoyer() { self.document.forms[0].submit(); }
function ret_id_forum() { return document.forms[0].elements["idForum"].value; }
function ret_id_sujet() { return document.forms[0].elements["idSujet"].value; }
function def_id_sujet(v_iIdSujet) {document.forms[0].elements["idSujet"].value = v_iIdSujet; }
function ret_liste_ids_sujets() { return document.forms[0].elements["idSujet[]"]; }
function rafraichir()
{
	var sUrl = "sujets.php"
		+ "?idForum=" + ret_id_forum()
		+ "&idSujet=" + ret_id_sujet()
		+ "&idEquipe=" + top.g_iIdEquipe;
	self.location.replace(sUrl);
	self.location = sUrl;
}
function rafraichir_infos_sujet() { afficher_infos_sujet(ret_id_sujet()); }
function afficher_indice(v_iIdSujet)
{
	var idNoeud = "id_indice_" + v_iIdSujet;
	if (document.getElementById)
	{
		if (idAncienNoeud != null) document.getElementById(idAncienNoeud).innerHTML = "&nbsp;";
		if (document.getElementById(idNoeud))
		{
			document.getElementById(idNoeud).innerHTML = "&raquo;";
			idAncienNoeud = idNoeud;
		}
	}
}
//-->
</script>
</head>
<body onload="init()" class="sujet">
<form action="{form->action}" method="get" target="SUJETS_LISTE">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
[BLOCK_SUJET+]
<tr class="ligne_normale" onmouseover="this.className='ligne_surlignee'" onmouseout="this.className='ligne_normale'" onclick="afficher_infos_sujet('{sujet->id}')">
<td><span id="id_indice_{sujet->id}">&nbsp;</span></td>
<td><span class="sujet_numero_ligne">{sujet->numero_ordre}.&nbsp;&nbsp;</span></td>
<td width="99%"><b><a href="javascript: void(0);" onclick="return afficher_infos_sujet('{sujet->id}')" onfocus="blur()"><span class="sujet_titre">{sujet->titre}</span></a></b></td>
<td align="right"><span class="sujet_nombre_messages">{sujet->messages}&nbsp;message(s)</span></td>
<td><img src="commun://espacer.gif" width="30" height="20"></td>
<td><span class="sujet_date_dernier_message">dernier&nbsp;:&nbsp;&nbsp;{sujet->dernier_poster}</span></td>
<td>{sujet->selecteur}</td>
</tr>
[BLOCK_SUJET-]
</table>
<input type="hidden" name="idForum" value="{forum->id}">
<input type="hidden" name="idSujet" value="{sujet->id}">
<input type="hidden" name="idEquipe" value="{equipe->id}">
</form>
</body>
</html>

