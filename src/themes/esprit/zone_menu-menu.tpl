<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://toute_zone.css">
<script type="text/javascript" language="javascript" src="javascript://indice.js"></script>
<script type="text/javascript" language="javascript" src="commun://js/zone_menu.js"></script>
<script type="text/javascript" language="javascript">
<!--

var asTitresFormation = {tableau_titres_formation};
var sDernierIdSpan;

function changerTitres(v_iPosTableau,v_sIdSpan,v_sHistorique)
{
	var iX = 0;
	var iY = 0;
	
	if (typeof(v_iPosTableau) != "undefined" && parseInt(v_iPosTableau) >= 0 && asTitresFormation.length > 0)
	{
		if (typeof(v_sHistorique) == "undefined")
			v_sHistorique = "&nbsp;";
		
		parent.changerTitres(asTitresFormation[v_iPosTableau],v_sHistorique);
	}
	
	if (typeof(v_sIdSpan) == "undefined")
	{
		// L'utilisateur a cliqué sur le nom de la formation, donc,
		// masquons l'indice de cours et mettons en évidence le nom
		// de la formation.
		masquerIndice("indice");
		sDernierIdSpan = null;
	}
	else if (document.getElementById(v_sIdSpan) &&
		document.getElementById(v_sIdSpan).style)
	{
		iX = getRealLeft(document.getElementById(v_sIdSpan));
		iY = getRealTop(document.getElementById(v_sIdSpan));
		
		if (navigator.userAgent.indexOf("Mac") != -1)
			deplacerIndice("indice",iX,iY,0,-2);
		else
			deplacerIndice("indice",iX,iY,+5,3);
		
		sDernierIdSpan = v_sIdSpan
	}
	
	return new Array(iX,iY);
}

function init()
{
	aCoord = changerTitres({numero_titre_formation}[BLOCK_ONLOAD+][BLOCK_ONLOAD-]);
	var iY = ((aCoord[1]-100) > 0 ? aCoord[1]-100 : 0);
	self.scrollTo(0,iY);
}
function oMenu()
{
return parent.frames["Menu"];
}
function rechargerMenuBas(v_iIdFormActuelle, v_iIdModActuel, v_sTypeAffichage)
{
var url;
	if (v_iIdModActuel == 0)
		url = "http://"+oMenu().location.hostname + oMenu().location.pathname + "?idForm="+v_iIdFormActuelle+"&idMod=0&idNiveau="+v_iIdFormActuelle+"&typeNiveau=1&sAffiche="+v_sTypeAffichage;		
	else
		url = "http://"+oMenu().location.hostname + oMenu().location.pathname + "?idForm="+v_iIdFormActuelle+"&idMod="+v_iIdModActuel+"&sAffiche="+v_sTypeAffichage;
	oMenu().location = url;
}
//-->
</script>
</head>
<body onload="init()" onresize="changerTitres(null,sDernierIdSpan,null)">
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<tr>
<td width="240px">
[BLOCK_FORMATION+]
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<tr>
<td class="bloc_menu_coins_haut" align="left" valign="top"><img src="theme://menu-1x1.gif" width="5" height="28" border="0"></td>
<td class="bloc_menu_titre" width="99%">{nom_formation}{id_formation}</td>
<td class="bloc_menu_coins_haut" align="right" valign="top"><img src="theme://menu-1x3.gif" width="5" height="28" border="0"></td>
</tr>
[BLOCK_COURS+][BLOCK_COURS-]
<tr>
<td class="bloc_menu_coins_bas" align="left" valign="bottom"><img src="theme://menu-3x1.gif" width="5" height="28" border="0"></td>
<td class="bloc_menu_coins_bas">&nbsp;</td>
<td class="bloc_menu_coins_bas" align="right" valign="bottom"><img src="theme://menu-3x3.gif" width="5" height="28" border="0"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
</table>
[BLOCK_FORMATION-]
</td>
</tr>
</table>
<div id="indice"><img src="theme://indice.gif" width="8" height="12" border="0"></div>
</body>
</html>
[SET_FORMATION_ONLOAD+],'descr_{id_onload}','Description'[SET_FORMATION_ONLOAD-]

[SET_MODULE_ONLOAD+],'mod_{id_onload}','{nom_cours_onload_encoder}'[SET_MODULE_ONLOAD-]

[SET_DESCRIPTION_FORMATION+]
<tr>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
<td class="description_menu"><span id="descr_{id_formation}">&nbsp;&nbsp;&nbsp;&nbsp;</span><a href="{href_description}" target="Principal" onclick="changerTitres({index_formation},'descr_{id_formation}','Description'); rechargerMenuBas({idFormAct}, 0,'{sTypeAffichage}');" onfocus="blur()">Description</a></td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_DESCRIPTION_FORMATION-]

[SET_SANS_DESCRIPTION_FORMATION+]
<tr><td class="element_actif" colspan="3">&nbsp;</td></tr>
[SET_SANS_DESCRIPTION_FORMATION-]

[SET_COURS+]
<tr>
<td class="element_actif" valign="top"><span id="mod_{id_cours}">&nbsp;&nbsp;&nbsp;</span></td>
<td class="element_actif">{cours}</td>
<td class="element_actif">&nbsp;&nbsp;&nbsp;</td>
</tr>
[SET_COURS-]

[SET_COURS_OUVERT+]
[BLOCK_COURS_INTITULE+]<span class="cours">{intitule_cours}</span>[BLOCK_COURS_INTITULE-]{separateur_intitule}<a href="{href_cours}" target="Principal" onclick="restorer_position_cours(); changerTitres({index_formation},'mod_{id_cours}','{nom_cours_encoder}');rechargerMenuBas({idFormAct}, {idModAct},'{sTypeAffichage}');" onfocus="blur()">{nom_cours}</a>
[SET_COURS_OUVERT-]

[SET_SEPARATEUR_INTITULE+]&nbsp;:&nbsp;[SET_SEPARATEUR_INTITULE-]

[SET_COURS_FERMER+]
[BLOCK_COURS_INTITULE+]<span class="cours">{intitule_cours}</span>[BLOCK_COURS_INTITULE-]{separateur_intitule}<span class="cours_ferme">{nom_cours}</span>
[SET_COURS_FERMER-]

[SET_SANS_COURS+]&nbsp;[SET_SANS_COURS-]
