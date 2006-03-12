<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire_zdc.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript" src="commun://js/formulaire.js"></script>
<script type="text/javascript" language="javascript" src="formulaire.js"></script>
</head>
<body>
<form>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
[BLOCK_FORMULAIRE+]
<tr><td align="center">{formulaire->element}</td></tr>
<tr><td>&nbsp;</td></tr>
[BLOCK_FORMULAIRE-]
</table>
</form>
<p>&nbsp;</p>
</body>
</html>

[SET_LISTE_ICONES+]
[ARRAY_LISTE_ICONES+]
<a href="{a.exporter.href}" class="exporter_donnees" onfocus="blur()" target="_self">Exporter les données</a>&nbsp;#@#
<a href="javascript: void(0);" onclick="{a.choix_courriel.href}; return false;" onfocus="blur()" title="Cliquer ici pour envoyer un courriel"><img src="commun://icones/24x24/courriel_envoye.gif" width="24" height="24" border="0"></a>&nbsp;
[ARRAY_LISTE_ICONES-]
<div style="width: 90%; height: 25px; text-align: right;">{liste_icones}</div>
[SET_LISTE_ICONES-]

[SET_DESCRIPTION+]
<table border="0" cellspacing="0" cellpadding="0" width="90%">
<tr><td class="description">{description->texte}</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
[SET_DESCRIPTION-]

[SET_DOCUMENT_DE_BASE+]
[VAR_TITRE+]Pour commencer[VAR_TITRE-]
[VAR_DOCUMENT_URL+]<img scr="commun://espacer.gif" width="15" height="1" border="0"><a class="formulaire_base" href="javascript: void(0);" onclick="{a->href}" onfocus="blur()">{a->label}</a><br>[VAR_DOCUMENT_URL-]
[VAR_CONSIGNE+]<br><div class="consigne">D&eacute;marrer une activit&eacute;&nbsp;? Cliquez sur l'intitul&eacute; rouge</div>[VAR_CONSIGNE-]
<table border="0" cellspacing="0" cellpadding="0" width="90%"><tr><td>{document_de_base}</td></tr></table>
[SET_DOCUMENT_DE_BASE-]

[SET_TRAVAUX_EN_COURS+]
<!--[[ Travaux en cours -->
[VAR_TITRE+]Travaux en cours[VAR_TITRE-]
[VAR_LISTE_DOCUMENTS+]
<table border="0" cellspacing="0" cellpadding="0">
{liste_documents}
</table>
[VAR_LISTE_DOCUMENTS-]
[VAR_LIGNE_DOCUMENT+]
<tr>
<td><img scr="commun://espacer.gif" width="10" height="1" border="0"></td>
<td>{document->selectionner}&nbsp;</td>
<td><b>Version&nbsp;{document->titre}</b> d&eacute;pos&eacute; par {document->personne_complet} le {document->date}</td>
</tr>
[VAR_LIGNE_DOCUMENT-]
[VAR_CONSIGNE+]
<br>
<div align="right"><a class="soumettre" href="javascript: void(0);">Soumettre</a></div>
<div class="consigne">Pour poursuivre, corriger un travail&nbsp;? Cliquez sur la version du travail (habituellement la derni&egrave;re)<br>
Pour soumettre le travail au tuteur&nbsp;? S&eacute;lectionner la version &agrave;; soumettre et cliquez sur &laquo;&nbsp;Soumettre&nbsp;&raquo;
</div>
[VAR_CONSIGNE-]
<table border="0" cellspacing="0" cellpadding="0" width="90%">
<tr><td>{onglet}</td></tr>
</table>
<!-- ]]-->
[SET_TRAVAUX_EN_COURS-]

[SET_TRAVAUX_SOUMIS+]
<!--[[ Travaux soumis -->
[VAR_TITRE+]Travaux soumis pour &eacute;valuation[VAR_TITRE-]
[VAR_LISTE_DOCUMENTS+]
<table border="0" cellspacing="0" cellpadding="0" width="100%">
{liste_documents}
<tr><td>&nbsp;</td><td colspan="2" style="text-align: right;">{evaluer->bouton}</td></tr>
<tr><td style="background-color: rgb(255,255,255);" colspan="3">&nbsp;</td></tr>
</table>
[VAR_LISTE_DOCUMENTS-]
[VAR_BOUTON_EVALUER+]
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Obtenir l'&eacute;valuation</a>###
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Evaluer</a>
[VAR_BOUTON_EVALUER-]
[VAR_BOUTON_SELECTIONNER_FORMULAIRE+]<input type="radio" name="{radio->name}" onclick="surbrillance('id_soumettre_{personne->id}')" value="{radio->value}" onfocus="blur()">[VAR_BOUTON_SELECTIONNER_FORMULAIRE-]
[VAR_LIGNE_DOCUMENT+]
<tr>
<td><img scr="commun://espacer.gif" width="20" height="1" border="0"></td>
<td>{document->selectionner}</td>
<td width="99%">&nbsp;&nbsp;<a href="javascript: void(0);" onclick="{a->href}" onfocus="blur()"><b>Version&nbsp;{document->titre}</b> soumis pour &eacute;valuation le {document->date}</a><span style="font-size: 8pt;">{document->evalue}</span></td>
</tr>
[VAR_LIGNE_DOCUMENT-]
[VAR_PAS_DOCUMENT_TROUVE+]
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td style="text-align: center; font-size: 7pt;">Pas de document trouv&eacute;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
[VAR_PAS_DOCUMENT_TROUVE-]
[VAR_CONSIGNE+]
<div class="consigne">Acc&eacute;der &agrave; l'&eacute;valuation&nbsp;? S&eacute;lectionnez la version et cliquez sur &laquo;&nbsp;Obtenir l'&eacute;valuation&nbsp;&raquo;</div>###
<div class="consigne">Evaluer&nbsp;? S&eacute;lectionner une version et cliquez sur &laquo;&nbsp;Evaluer&nbsp;&raquo;</div>
[VAR_CONSIGNE-]
[VAR_FORMULAIRE_EVALUATION+]
###
###
###
&nbsp;&nbsp;<img src="theme://formulaire/res_non_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(non &eacute;valu&eacute;)###
&nbsp;&nbsp;<img src="theme://formulaire/res_a_poursuivre.gif" width="8" height="8" border="0">&nbsp;&nbsp;(&eacute;valu&eacute;&nbsp;: &agrave; poursuivre)###
&nbsp;&nbsp;<img src="theme://formulaire/res_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(&eacute;valu&eacute;&nbsp;: activit&eacute; termin&eacute;e)
[VAR_FORMULAIRE_EVALUATION-]
<table border="0" cellspacing="0" cellpadding="0" width="90%">
<tr><td>{onglet}</td></tr>
</table>
<!-- Travaux soumis ]]-->
[SET_TRAVAUX_SOUMIS-]

