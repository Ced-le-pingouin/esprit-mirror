<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/forum.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="forum.js"></script>
<script type="text/javascript" language="javascript">
<!--
function init() { top.g_iIdEquipe = '{equipe->id}'; }
function changer_id_equipe(v_iIdEquipe)
{
	top.g_iIdEquipe = v_iIdEquipe;
	document.forms[0].elements["idEquipe"].value = v_iIdEquipe;
	top.oFrmListeSujets().document.forms[0].elements["idEquipe"].value = v_iIdEquipe;
	top.oFrmListeSujets().rafraichir();
}
function ret_id_forum() { return document.forms[0].elements["idForum"].value; }
function def_id_sujet(v_iIdSujet) { document.forms[0].elements["idSujet"].value = v_iIdSujet; }

function exporter() {
	var sUrl = "forum_export-index.php"
		+ "?idForum=" + ret_id_forum();
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"winForumExport",320,300,sOptionsFenetre);
	win.focus();
}
//-->
</script>
</head>
<body onload="init()" class="changer_sujet">
<form action="forum-sujets.php" method="get" target="SUJETS" style="padding: 8 0 0 0;">
{liste_sujets}
<input type="hidden" name="idForum" value="{forum->id}">
<input type="hidden" name="idSujet" value="{sujet->id}">
<input type="hidden" name="idNiveau" value="{niveau->id}">
<input type="hidden" name="typeNiveau" value="{niveau->type}">
<input type="hidden" name="idEquipe" value="{equipe->id}">
</form>
<div style="border: rgb(0,0,0) none 1px; position: absolute; left: 0px; top: 0px; width: 100%; text-align: right;"><a href="javascript: void(0);" onclick="{envoi_courriel}; return false;" onfocus="blur()" title="Cliquer ici pour envoyer un courriel"><img src="commun://icones/24x24/courriel_envoye.gif" border="0" height="24" width="24"></a>&nbsp;&nbsp;&nbsp;</div>
</body>
</html>

[SET_LISTE_SUJETS+]
<iframe name="SUJETS_LISTE" src="{iframe->src}" frameborder="0" marginwidth="0" marginheight="0" height="130" width="100%" scrolling="yes"></iframe>
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
[BLOCK_MENU_SUJETS+][BLOCK_MENU_SUJETS-]
<td>&nbsp;&nbsp;</td>
</tr>
</table>
[SET_LISTE_SUJETS-]

[SET_LISTE_SUJETS_EQUIPES+]
<div style="text-align: right; margin: 2px;"><span class="intitule">Liste des &eacute;quipes&nbsp;:&nbsp;</span><select name="selectIdEquipe" onchange="changer_id_equipe(this.options[this.selectedIndex].value)">[BLOCK_EQUIPE+]<option value="{equipe->id}"{option->selected}>{equipe->nom}</option>[BLOCK_EQUIPE-]</select><br></div>
<iframe name="SUJETS_LISTE" src="{iframe->src}" frameborder="0" marginwidth="0" marginheight="0" height="110" width="100%" scrolling="yes"></iframe>
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
[BLOCK_MENU_SUJETS+][BLOCK_MENU_SUJETS-]
<td>&nbsp;&nbsp;</td>
</tr>
</table>
[SET_LISTE_SUJETS_EQUIPES-]

[SET_NOUVEAU_SUJET_EQUIPES+]<td class="menu_ajouter_sujet_pour_tous" nowrap="nowrap"><a href="javascript: void(0);" onclick="popup_nouveau_sujet('{forum->id}','0',true); return false;" onfocus="blur()">Nouveau sujet (toutes les &eacute;quipes)</a></td>[SET_NOUVEAU_SUJET_EQUIPES-]
[SET_NOUVEAU_SUJET+]<td class="menu_ajouter_sujet" nowrap="nowrap"><a href="javascript: void(0);" onclick="popup_nouveau_sujet('{forum->id}','{sujet->equipe->id}',false); return false;" onfocus="blur()">Sujet</a></td>[SET_NOUVEAU_SUJET-]
[SET_MODIFIER_SUJET+]<td class="menu_modifier_sujet"><a href="javascript: void(0);" onclick="popup_modifier_sujet(); return false;" onfocus="blur()">Modifier</a></td>[SET_MODIFIER_SUJET-]
[SET_SUPPRIMER_SUJET+]<td class="menu_supprimer_sujet"><a href="javascript: void(0);" onclick="popup_supprimer_sujet(); return false;" onfocus="blur()">Supprimer</a></td>[SET_SUPPRIMER_SUJET-]
[SET_MENU_SEPARATEUR+]<td>&nbsp;&nbsp;</td>[SET_MENU_SEPARATEUR-]
