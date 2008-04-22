<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://equipe.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_sNomId = null;

function surbrillance(v_sNomId,v_bBrillant)
{
	if (!document.getElementById || !document.getElementById(v_sNomId)) return;
	if (g_sNomId != null) { document.getElementById(g_sNomId).style.color = "rgb(0,0,0)"; g_sNomId = null; }
	if (v_bBrillant) { document.getElementById(v_sNomId).style.color = "rgb(201,91,40)"; g_sNomId = v_sNomId; }
}

function init()
{
	top.changerSousTitre("{activite.nom}");
	
	if (top.document.getElementsByName)
		top.document.getElementsByName("Bas").item(0).setAttribute("src","{frame.menu.src}");
}
//-->
</script>
</head>
<body onload="init()">
<div id="barre_outils">[BLOCK_BARRE_OUTILS+][BLOCK_BARRE_OUTILS-]</div>
[BLOCK_TUTEURS+]
[VAR_TITRE+]Liste des tuteurs[VAR_TITRE-]
[VAR_MEMBRE_NON_TROUVE+]<p style="text-align: center;">Auncun tuteur n'a &eacute;t&eacute; d&eacute;tect&eacute;</p>[VAR_MEMBRE_NON_TROUVE-]
[VAR_MEMBRES+]
<table border="0" cellpadding="2" cellspacing="1" width="100%">
{liste_membres}
</table>
[VAR_MEMBRES-]
[BLOCK_TUTEURS-]
[BLOCK_EQUIPES+]
[VAR_TITRE+]Liste des &eacute;quipes###{equipe.nom}[VAR_TITRE-]
[VAR_MEMBRE_NON_TROUVE+]<p style="text-align: center;">Aucune &eacute;quipe n'a &eacute;t&eacute; d&eacute;tect&eacute;e</p>[VAR_MEMBRE_NON_TROUVE-]
[VAR_MEMBRES+]
<table border="0" cellpadding="2" cellspacing="1" width="100%">
{liste_membres}
</table>
[VAR_MEMBRES-]
[BLOCK_EQUIPES-]
</body>
</html>

[SET_FICHE_PERSONNE+]
<tr>
<td rowspan="2" valign="top">{personne.sexe}</td>
<td width="99%"><span style="font-size: 8pt; font-weight: bold;"><span id="id_surbrillance_{personne.index}">{personne.nom}&nbsp;{personne.prenom}&nbsp;{personne.indice}</span></td>
<td nowrap="nowrap"><span onmouseover="surbrillance('id_surbrillance_{personne.index}',true)" onmouseout="surbrillance('id_surbrillance_{personne.index}',false)">{icones}</span></td>
</tr>
<tr>
<td colspan="2"><div style="font-size: 8pt; border: rgb(174,165,138) none 1px; border-top-style: dashed; width: 100%;">{personne.pseudo}</div><img src="commun://espacer.gif" width="1" height="8" border="0"></td>
</tr>
[SET_FICHE_PERSONNE-]

[SET_SEXE_MASCULIN+]<img src="commun://icones/boy.gif" width="14" height="26" border="0">[SET_SEXE_MASCULIN-]
[SET_SEXE_FEMININ+]<img src="commun://icones/girl.gif" width="15" height="26" border="0">[SET_SEXE_FEMININ-]
[SET_COURRIEL+]<a href="javascript: void(0);" onclick="choix_courriel('{a.choix_courriel.href}&typeCourriel=courriel-cours@cours'); return false;" onfocus="blur()" title="Cliquer ici pour envoyer un courriel"><img src="commun://icones/mail.gif" width="16" height="16" border="0"></a>[SET_COURRIEL-]
[SET_SANS_COURRIEL+]<img src="commun://icones/pas_mail.gif" width="16" height="16" border="0">[SET_SANS_COURRIEL-]
[SET_INDICE+]<img src="theme://icones/etoile.gif" width="13" height="13" border="0">[SET_INDICE-]
[SET_SEPARATEUR_BLOC+]<img src="commun://espacer.gif" border="0" height="15" width="100%">[SET_SEPARATEUR_BLOC-]
