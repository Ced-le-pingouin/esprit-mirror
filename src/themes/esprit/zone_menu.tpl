<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://zone_menu.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
[BLOCK_HTML_HEAD+][BLOCK_HTML_HEAD-]
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="commun://js/zone_menu.js"></script>
<script type="text/javascript" language="javascript">
<!--
function tchatche(v_iIdNiveau,v_iTypeNiveau)
{
	var sUrl = "{chat.url}"
		+ "?idNiveau=" + v_iIdNiveau
		+ "&typeNiveau=" + v_iTypeNiveau;
	var winTchatcheRub = PopupCenter(sUrl,("winTchatcheRub" + v_iIdNiveau),600,410,",toolbar=no,resizable=yes,scrollbars=no");
	winTchatcheRub.focus();
}

function texte_formatte(v_iIdNiveau,v_iTypeNiveau)
{
	var sUrl = "{texte_formatte.url}"
		+ "?idNiveau=" + v_iIdNiveau
		+ "&typeNiveau=" + v_iTypeNiveau;
	var iLargeurFenetre = (screen.width-250);
	var iHauteurFenetre = (screen.height-150);
	var oWinTexteFormatteRub = PopupCenter(sUrl,("winTexteFormatteRub" + v_iIdNiveau),iLargeurFenetre,iHauteurFenetre,",toolbar=no,resizable=yes,scrollbars=yes");
	oWinTexteFormatteRub.focus();
}

function init()
{
	positionner_cours();
	if (top.changerStatutUtilisateur)
		top.changerStatutUtilisateur("{personne.statut:urlencode}");
}
//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
<td width="99%">
<table border="0" cellspacing="1" cellpadding="3" width="100%">
[BLOCK_SANS_FORMATIONS+]<tr><td><p class="sans_formation">Aucune formation n'est disponible pour l'instant</p></td></tr>[BLOCK_SANS_FORMATIONS-]
[BLOCK_TITRE_COURS+]
[VAR_SEPARATEUR_INTITULE_COURS+]&nbsp;:&nbsp;&nbsp;&nbsp;[VAR_SEPARATEUR_INTITULE_COURS-]
<tr>
<td width="1%">&nbsp;</td>
<td colspan="4" style="border: rgb(174,165,138) none 1px; border-bottom-style: dashed; color: rgb(171,161,142); font-size: 17pt; font-weight: bold;">{cours.intitule}{cours.titre}</td>
</tr>
<tr><td style="text-align: right;" colspan="5">{outils.tableau_de_bord}&nbsp;{outils.choix_courriel}&nbsp;{outils.liste_inscrits}</td></tr>
[BLOCK_TITRE_COURS-]
[BLOCK_DESCRIPTION+]
<tr>
<td>&nbsp;</td>
<td colspan="4" align="center">
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr><td><img src="commun://espacer.gif" width="100%" height="10" alt=""></td></tr>
<tr><td class="description">{description_cours}</td></tr>
<tr><td><img src="commun://espacer.gif" width="100%" height="10" alt=""></td></tr>
</table>
</td>
</tr>
[BLOCK_DESCRIPTION-]
[BLOCK_COURS+][BLOCK_COURS-]
</table>
</td>
<td valign="top" align="center">&nbsp;</td>
</tr>
</table>
<p>&nbsp;</p>
[BLOCK_APPLET_AWARENESS+]{applet_awareness}[BLOCK_APPLET_AWARENESS-]
</body>
</html>

[SET_FORUM+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="1" border="0"></td>
<td colspan="4" width="99%">{lien_forum}</td>
</tr>
[SET_FORUM-]
[SET_FORUM_OUVERT+]<a href="javascript: void(0);" onclick="return forum('{rubrique.url}','WinForumRub{rubrique.id}')" onfocus="blur()">{rubrique.nom}</a>[SET_FORUM_OUVERT-]
[SET_FORUM_FERME+]<span class="unite_passif">{rubrique.nom}</span>[SET_FORUM_FERME-]

[SET_UNITE+]
[VAR_UNITE+]
<td colspan="2" style="width: 99%;">{lien_unite}</td>###
<td align="right" nowrap="nowrap"><span class="intitule_unite">{rubrique.intitule}&nbsp;:</span></td>
<td style="width: 99%;">{lien_unite}</td>
[VAR_UNITE-]
[VAR_UNITE_OUVERT+]<a class="unite" href="{rubrique.url}" onclick="sauver_position_cours()" target="INDEX">{rubrique.nom}</a>[VAR_UNITE_OUVERT-]
[VAR_UNITE_FERME+]<span class="unite_passif">{rubrique.nom}</span>[VAR_UNITE_FERME-]
<tr>
<td><img src="commun://espacer.gif" width="10" height="1" border="0"></td>
<td style="border: rgb(202,195,177) none 1px; border-right-style: dotted;">&nbsp;</td>
<td>&nbsp;</td>
{lien_unite}
</tr>
[SET_UNITE-]

[SET_UNITE_ESPACE+]
<tr>
<td>&nbsp;</td>
<td colspan="3"><img src="commun://espacer.gif" width="1" height="1" border="0"></td>
<td style="width: 99%;">&nbsp;</td>
</tr>
[SET_UNITE_ESPACE-]

[SET_PAGE_HTML+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{lien_page_html}</td>
</tr>
[SET_PAGE_HTML-]
[SET_PAGE_HTML_OUVERT+]<a href="{rubrique.url}" target="_blank" onfocus="blur()">{rubrique.nom}</a>[SET_PAGE_HTML_OUVERT-]
[SET_PAGE_HTML_FERME+]<span title="Ce lien est ferm&eacute;" class="unite_passif">{rubrique.nom}</span>[SET_PAGE_HTML_FERME-]

[SET_SITE_INTERNET+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{lien_site_internet}</td>
</tr>
[SET_SITE_INTERNET-]
[SET_SITE_INTERNET_OUVERT+]<a href="{rubrique.url}" target="_blank" onfocus="blur()">{rubrique.nom}</a>[SET_SITE_INTERNET_OUVERT-]
[SET_SITE_INTERNET_FERME+]<span title="Ce lien est ferm&eacute;" class="unite_passif">{rubrique.nom}</span>[SET_SITE_INTERNET_FERME-]

[SET_DOCUMENT_TELECHARGER+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{lien_document_telecharger}</td>
</tr>
[SET_DOCUMENT_TELECHARGER-]
[SET_DOCUMENT_TELECHARGER_OUVERT+]<a href="{rubrique.url}" title="T&eacute;l&eacute;charger ce document" target="_self" onfocus="blur()">{rubrique.nom}</a>[SET_DOCUMENT_TELECHARGER_OUVERT-]
[SET_DOCUMENT_TELECHARGER_FERME+]<span title="Ce lien est ferm&eacute;" class="unite_passif">{rubrique.nom}</span>[SET_DOCUMENT_TELECHARGER_FERME-]

[SET_CHAT+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{lien_chat}</td>
</tr>
[SET_CHAT-]
[SET_CHAT_OUVERT+]<a href="javascript: void(0);" onclick="tchatche('{rubrique.id}','{rubrique.niveau.id}'); return false;" onfocus="blur()">{rubrique.nom}</a>[SET_CHAT_OUVERT-]
[SET_CHAT_FERME+]<span title="Ce lien est ferm&eacute;" class="unite_passif">{rubrique.nom}</span>[SET_CHAT_FERME-]

[SET_TEXTE_FORMATTE+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{lien_texte_formatte}</td>
</tr>
[SET_TEXTE_FORMATTE-]
[SET_TEXTE_FORMATTE_OUVERT+]<a href="javascript: void(0);" onclick="texte_formatte('{rubrique.id}','{rubrique.niveau.id}'); return false;" onfocus="blur()">{rubrique.nom}</a>[SET_TEXTE_FORMATTE_OUVERT-]
[SET_TEXTE_FORMATTE_FERME+]<span title="Ce lien est ferm&eacute;" class="unite_passif">{rubrique.nom}</span>[SET_TEXTE_FORMATTE_FERME-]

[SET_INTITULE_NONACTIV+]
<tr>
<td><img src="commun://espacer.gif" width="10" height="10" border="0"></td>
<td colspan="4" width="99%">{nonactiv.nom}</td>
</tr>
[SET_INTITULE_NONACTIV-]