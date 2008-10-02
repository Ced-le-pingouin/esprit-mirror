<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://toute_zone.css">
[BLOCK_HTML_HEAD+][BLOCK_HTML_HEAD-]
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://indice.js"></script>
<script type="text/javascript" language="javascript">
<!--
var asHistoriques = new Array({tableau_historiques});
var sDernierIdSpan = null;

function changerHistorique(v_iPosTableau,v_sIdSpan)
{
	var iX = 0;
	var iY = 0;
	
	if (typeof(v_iPosTableau) != "undefined" && parseInt(v_iPosTableau) >= 0)
		parent.changerTitres(null,asHistoriques[v_iPosTableau]);
	
	if (typeof(v_sIdSpan) == "undefined")
	{
		// L'utilisateur a cliqué sur le nom de la formation, donc,
		// masquons l'indice de cours et mettons en évidence le nom
		// de la formation.
		masquerIndice("indice");
		sDernierIdSpan = null;
	}
	else if (document.getElementById(v_sIdSpan))
	{
		iX = getRealLeft(document.getElementById(v_sIdSpan));
		iY = getRealTop(document.getElementById(v_sIdSpan));
		
		if (navigator.userAgent.indexOf("Mac") != -1)
			deplacerIndice("indice",iX,iY,0,-2);
		else
			deplacerIndice("indice",iX,iY,0,3);
		
		sDernierIdSpan = v_sIdSpan;
	}
	
	return new Array(iX,iY);
}

function tchatche(v_iIdNiveau,v_iTypeNiveau)
{
	var sUrl = "{chat.url}"
		+ "?idNiveau=" + v_iIdNiveau
		+ "&typeNiveau=" + v_iTypeNiveau;
	var winTchatcheSA = PopupCenter(sUrl,("winTchatcheSA" + v_iIdNiveau),600,410,",toolbar=no,resizable=yes,scrollbars=no");
	winTchatcheSA.focus();
}

function texte_formatte(v_iIdNiveau,v_iTypeNiveau)
{
	var sUrl = "{texte_formatte.url}"
		+ "?idNiveau=" + v_iIdNiveau
		+ "&typeNiveau=" + v_iTypeNiveau;
	var iLargeurFenetre = (screen.width-250);
	var iHauteurFenetre = (screen.height-150);
	var oWinTexteFormatteSA = PopupCenter(sUrl,("winTexteFormatteSA" + v_iIdNiveau),iLargeurFenetre,iHauteurFenetre,",toolbar=no,resizable=yes,scrollbars=yes");
	oWinTexteFormatteSA.focus();
}

function equipes(v_iIdActiv)
{
	var sUrl = "{equipes.url}"
		// + "?idActiv=" + v_iIdActiv
		+ "?idStatuts={statut.tuteur.id}"
		+ "&idEquipes=tous"
		+ "&typeCourriel=courriel-unite";
	var winEquipesSA = PopupCenter(sUrl,"winEquipesSA",600,410,",toolbar=no,resizable=yes,scrollbars=no");
	winEquipesSA.focus();
	return false;
}

function init()
{
	[BLOCK_FONCTION_INIT+][BLOCK_FONCTION_INIT-]
}

function oMenu()
{
return parent.frames["Bas"];
}
function rechargerMenuBas(v_iIdActiv, v_iIdSousActiv)
{
var url;
// on récupère toutes les variables avant idActiv pour éviter de se retrouver avec plusieurs variables idActiv et idSousActiv de suite.
var_temp = oMenu().location.search.split("idActiv");
url = "http://"+oMenu().location.hostname + oMenu().location.pathname + var_temp[0] + "idActiv="+v_iIdActiv+"&idSousActiv="+v_iIdSousActiv;
oMenu().location = url;
}
//-->
</script>
</head>
<body onload="init()" onresize="changerHistorique(null,sDernierIdSpan)">
[BLOCK_BLOC+]
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<tr>
<td align="left" valign="top" class="bloc_menu_coins_haut"><img src="theme://menu-1x1.gif" width="5" height="28" border="0"></td>
<td class="bloc_menu_titre" width="99%">{nom_bloc}</td>
<td align="right" valign="top" class="bloc_menu_coins_haut"><img src="theme://menu-1x3.gif" width="5" height="28" border="0"></td>
</tr>
[BLOCK_SOUS_ACTIVITE+][BLOCK_SOUS_ACTIVITE-]
<tr>
<td align="left" valign="bottom" class="bloc_menu_coins_bas"><img src="theme://menu-3x1.gif" width="5" height="28" border="0"></td>
<td class="bloc_menu_coins_bas">&nbsp;</td>
<td align="right" valign="bottom" class="bloc_menu_coins_bas"><img src="theme://menu-3x3.gif" width="5" height="28" border="0"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
</table>
[BLOCK_BLOC-]
<div id="indice"><img src="theme://indice.gif" width="8" height="12" border="0"></div>
</body>
</html>

[SET_LIEN_FRAME_PRINCIPALE+]<a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}')" onfocus="blur()">{sousactiv.nom}</a>[SET_LIEN_FRAME_PRINCIPALE-]

[SET_SANS_SOUS_ACTIVITE+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="lien_passif" align="center">&nbsp;</td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_SANS_SOUS_ACTIVITE-]

[SET_LIEN_DESACTIVER+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="lien_passif" width="99%">{sousactiv.nom}</td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_LIEN_DESACTIVER-]

[SET_EQUIPE+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="element_equipe" width="99%"><a href="javascript: void(0);" onclick="return equipes('{id_bloc}')" onfocus="blur()">Equipe</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_EQUIPE-]

[SET_PAGE_HTML_FRAME_CENTRALE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_PAGE_HTML_FRAME_CENTRALE-]

[SET_PAGE_HTML_NOUVELLE_FENETRE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="javascript: void(0);" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas} window.open('{sousactiv.lien}','NFD','width=640,height=480,menubar=0,resizable=1,scrollbars=1');" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_PAGE_HTML_NOUVELLE_FENETRE-]

[SET_COLLECTICIEL+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_COLLECTICIEL-]

[SET_GALERIE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_GALERIE-]

[SET_DOCUMENT_TELECHARGER+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="{sousactiv.lien}" target="{sousactiv.lien.cible}" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); " onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_DOCUMENT_TELECHARGER-]

[SET_FORUM+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="element_actif" width="99%"><a href="javascript: void(0);" title="{sousactiv.infobulle}" onclick="return {forum.url}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_FORUM-]

[SET_CHAT+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="element_actif" width="99%"><a href="javascript: void(0);" title="{sousactiv.infobulle}" onclick="{chat.lien}; return false;" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_CHAT-]

[SET_SITE_INTERNET+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="{sousactiv.lien}" target="{sousactiv.lien.cible}" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_SITE_INTERNET-]

[SET_TEXTE_FORMATTE_FRAME_PRINCIPALE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_TEXTE_FORMATTE_FRAME_PRINCIPALE-]

[SET_TEXTE_FORMATTE_NOUVELLE_FENETRE+]
<tr>
<td class="element_actif">&nbsp;</td>
<td class="element_actif"><a href="javascript: void(0);" title="{sousactiv.infobulle}" onclick="texte_formatte('{sousactiv.id}','{sousactiv.niveau.id}'); return false; {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_TEXTE_FORMATTE_NOUVELLE_FENETRE-]

[SET_FORMULAIRE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif" width="99%"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_FORMULAIRE-]

[SET_GLOSSAIRE+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif"><a href="{sousactiv.lien}" target="Principal" title="{sousactiv.infobulle}" onclick="changerHistorique({sousactiv.ordre},'{sousactiv.signet}'); {sousactiv.rechargerBas}" onfocus="blur()">{sousactiv.nom}</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_GLOSSAIRE-]

[SET_TABLEAU_DE_BORD+]
<tr>
<td class="element_actif" valign="top">&nbsp;<span id="{sousactiv.signet}">&nbsp;</span>&nbsp;</td>
<td class="element_actif">{sousactiv.lien}</td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_TABLEAU_DE_BORD-]

[SET_PREMIERE_PAGE_FRAME_PRINCIPALE+]
	setTimeout("parent.premierePage('{premiere_page.lien}')",500);
	aCoord = changerHistorique({premiere_page.ordre_historique},'{premiere_page.signet}');
	var iY = ((aCoord[1]-100) > 0 ? aCoord[1]-100 : 0);
	self.scrollTo(0,iY);
[SET_PREMIERE_PAGE_FRAME_PRINCIPALE-]

[SET_PREMIERE_PAGE_NOUVELLE_FENETRE+]
	PopupCenter('{premiere_page.lien}','winZoneDeCours{sousactiv.id}',662,506,',menubar=no,scrollbars=yes,statusbar=no,resizable=yes');
[SET_PREMIERE_PAGE_NOUVELLE_FENETRE-]

[SET_PREMIERE_PAGE_CHAT+]
	tchatche('{sousactiv.id}','{sousactiv.type}')
[SET_PREMIERE_PAGE_CHAT-]

[SET_PREMIERE_PAGE_FORUM+]
	forum('{forum.url}','WinForumSA{sousactiv.id}');
[SET_PREMIERE_PAGE_FORUM-]

[SET_PREMIERE_PAGE_TEXTE_FORMATTE+]
	texte_formatte('{sousactiv.id}','{sousactiv.niveau.id}');
[SET_PREMIERE_PAGE_TEXTE_FORMATTE-]

[SET_PREMIERE_TABLEAU_DE_BORD+]
	if (document.getElementById
		&& document.getElementById("id_tableau_de_bord_{sousactiv.id}"))
	tableau_de_bord(document.getElementById("id_tableau_de_bord_{sousactiv.id}"));
[SET_PREMIERE_TABLEAU_DE_BORD-]

