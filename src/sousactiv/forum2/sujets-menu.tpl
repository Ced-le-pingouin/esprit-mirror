<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="/ipfhainaut_dev/themes/ipfhainaut/globals.css">
<link type="text/css" rel="stylesheet" href="/ipfhainaut_dev/themes/ipfhainaut/forum/sujets-menu.css">
<script type="text/javascript" language="javascript" src="/ipfhainaut_dev/js/dom.window.js"></script>
<script type="text/javascript" language="javascript" src="/ipfhainaut_dev/js/window.js"></script>
<script type="text/javascript" language="javascript" src="forum.js"></script>
<script type="text/javascript" language="javascript">
<!--
function init()
{
	afficher_infos_sujet('{sujet->id}');
}
//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="18">
<tr>
<td><img src="commun://espacer.gif" width="15" height="1" border="0"></td>
[BLOCK_SUJET_MENU+]
[VAR_NOUVEAU_SUJET_EQUIPES+]<td class="menu_ajouter_sujet_pour_tous" nowrap="nowrap"><a href="javascript: void(0);" onclick="popup_nouveau_sujet('{forum->id}',true); return false;" onfocus="blur()">Nouveau sujet (toutes les &eacute;quipes)</a></td>[VAR_NOUVEAU_SUJET_EQUIPES-]
[VAR_NOUVEAU_SUJET+]<td class="menu_ajouter_sujet" nowrap="nowrap"><a href="javascript: void(0);" onclick="popup_nouveau_sujet('{forum->id}',{forum->par_equipe}); return false;" onfocus="blur()">Nouveau sujet</a></td>[VAR_NOUVEAU_SUJET-]
[VAR_MODIFIER_SUJET+]<td class="menu_modifier_sujet"><a href="javascript: void(0);" onclick="popup_modifier_sujet(); return false;" onfocus="blur()">Modifier</a></td>[VAR_MODIFIER_SUJET-]
[VAR_SUPPRIMER_SUJET+]<td class="menu_supprimer_sujet"><a href="javascript: void(0);" onclick="popup_supprimer_sujet(); return false;" onfocus="blur()">Supprimer</a></td>[VAR_SUPPRIMER_SUJET-]
[VAR_MENU_SEPARATEUR+]<td>&nbsp;&nbsp;</td>[VAR_MENU_SEPARATEUR-]
{sujet_menu}
[BLOCK_SUJET_MENU-]
<td><img src="commun://espacer.gif" width="15" height="1" border="0"></td>
</tr>
</table>
</body>
</html>

