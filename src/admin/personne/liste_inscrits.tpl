<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">

[BLOCK_HTML_HEAD+][BLOCK_HTML_HEAD-]
<script type="text/javascript" language="javascript">
<!--
var g_sNomId = null;

function surbrillance(v_sNomId,v_bBrillant)
{
	if (!document.getElementById || !document.getElementById(v_sNomId))
		return;
	
	if (g_sNomId != null)
	{
		document.getElementById(g_sNomId).style.color = "rgb(0,0,0)";
		g_sNomId = null;
	}
	
	if (v_bBrillant)
	{
		document.getElementById(v_sNomId).style.color = "rgb(201,91,40)";
		g_sNomId = v_sNomId;
	}
}

function init()
{
	top.changerSousTitre('{module->nom}');
}
//-->
</script>
</head>
<body onload="init()">
[BLOCK_LISTES+][BLOCK_LISTES-]
</body>
</html>

[SET_LISTE_PERSONNES+]
<table border="0" cellspacing="0" cellpadding="2" width="100%">
{personnes->liste}
</table>
[SET_LISTE_PERSONNES-]

[SET_FICHE_PERSONNE+]
<tr>
<td rowspan="2" valign="top">{personne->alias}</td>
<td width="99%"><span style="font-size: 8pt; font-weight: bold;"><span id="id_surbrillance_{personne->index}">{personne->nom}&nbsp;{personne->prenom}&nbsp;{personne->indice}</span></td>
<td nowrap="nowrap"><span onmouseover="surbrillance('id_surbrillance_{personne->index}',true)" onmouseout="surbrillance('id_surbrillance_{personne->index}',false)">{personne->mail}</span></td>
</tr>
<tr>
<td colspan="2"><div style="font-size: 8pt; border: rgb(174,165,138) none 1px; border-top-style: dashed; width: 100%;">{personne->pseudo}</div><img src="commun://espacer.gif" width="1" height="8" border="0"></td>
</tr>
[SET_FICHE_PERSONNE-]

[SET_SEXE_MASCULIN+]<img src="commun://icones/boy.gif" width="14" height="26" border="0">[SET_SEXE_MASCULIN-]
[SET_SEXE_FEMININ+]<img src="commun://icones/girl.gif" width="15" height="26" border="0">[SET_SEXE_FEMININ-]
[SET_TRACE_CONNEXION_INDIVIDUEL+]<a href="javascript: void(0);" onclick="{personne->trace}" title="Trace de connexion de cet utilisateur" onfocus="blur()"><img src="commun://icones/16x16/trace.gif" width="16" height="16" border="0"></a>[SET_TRACE_CONNEXION_INDIVIDUEL-]
[SET_EMAIL+]<a href="mailto:{personne->mail}" title="Envoyer un mail" onfocus="blur()"><img src="commun://icones/mail.gif" width="16" height="16" border="0"></a>[SET_EMAIL-]
[SET_NON_EMAIL+]<img src="commun://icones/pas_mail.gif" width="16" height="16" border="0">[SET_NON_EMAIL-]
[SET_AUNCUN_INSCRIT+]
<table border="0" cellspacing="50" cellpadding="0" width="100%" height="100%">
<tr><td class="attention">Aucun inscrit &agrave; ce cours</td></tr>
</table>
[SET_AUNCUN_INSCRIT-]
[SET_INDICE+]<img src="theme://icones/etoile.gif" width="13" height="13" border="0">[SET_INDICE-]

[SET_ENVOI_COURRIEL_INSCRITS+]
<div style="width: 100%; text-align: right;">
<a href="javascript: void(0);" style="font-size: 7pt;" onclick="{a['envoi_courriel'].href}" onfocus="blur()">Envoi courriel &agrave; tous les &eacute;tudiants</a>
</div>
[SET_ENVOI_COURRIEL_INSCRITS-]

