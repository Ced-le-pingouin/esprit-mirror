<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript" src="commun://js/formulaire.js"></script>
<script type="text/javascript" language="javascript" src="formulaire.js"></script>
<style type="text/css">
<!--
.InterER
{
    margin-top: {iInterEnonRep}px;
}
.InterObj
{
    margin-top: {iInterElem}px;
}
.feedback, .feedback_titre {
display:none;
}
-->
</style>
</head>
<body>
[BLOCK_FORMULAIRE+]
<form>
{formulaire->listeIcones}
{formulaire->description}

<div id="formulaire_liste" style="width:100%">
    <table border="0" cellspacing="0" cellpadding="0" width="100%" style="/*margin-right: auto;margin-left: auto;*/">
        <tr>
            <td>&nbsp;</td>
            <td><img border="0" src="theme://onglet/onglet_tab-1x1.gif"></td>
            <td nowrap="nowrap" class="onglet_tab_1x2">&nbsp;Activit&eacute;s {utilisateur->nomComplet}&nbsp;</td>
            <td><img border="0" src="theme://onglet/onglet_tab-1x3.gif"></td>
            <td width="99%" style="border-bottom: 1px solid #DEE6E6;">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><img border="0" width="10" height="10" src="theme://onglet/onglet_tab_rect-1x1.gif"></td>
            <td colspan="4"><img border="0" width="1" height="1" src="theme://espacer.gif"></td>
            <td><img border="0" width="10" height="10" src="theme://onglet/onglet_tab_rect-1x3.gif"></td>
        </tr>
        <tr>
            <td style="background-image: url('theme://onglet/onglet_tab_rect-2x1.gif'); background-repeat: repeat-y;">&nbsp;</td>
            <td colspan="4">
                <p id="docbase">{formulaire->docBase}</p>
                <p id="encours">{formulaire->travauxEnCours}</p>
                <hr class="hr1">
                <p>{formulaire->travauxSoumis}</p>
                <p style="text-align: center; font-size: 7pt;">{formulaire->aucunEtudiant}</p>
            </td>
            <td style="background-image: url('theme://onglet/onglet_tab_rect-2x3.gif'); background-repeat: repeat-y;">&nbsp;</td>
        </tr>
        <tr>
            <td><img border="0" width="10" height="10" src="theme://onglet/onglet_tab_rect-3x1.gif"></td>
            <td colspan="4" style="border-bottom: 1px solid #DEE6E6;"><img border="0" width="1" height="1" src="theme://espacer.gif"></td>
            <td><img border="0" width="10" height="10" src="theme://onglet/onglet_tab_rect-3x3.gif"></td>
        </tr>
    </table>
</div>

<!--<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td align="center">{formulaire->element}</td></tr>
<tr><td>&nbsp;</td></tr>
</table>-->

</form>
<a name="FormulaireInline"></a>
[BLOCK_FORMULAIRE-]
[BLOCK_FORM_INLINE+]

<div id="formulaire_inline">
<p id="titre_formulaire_inline">{NomComplet_etudiant}{Info_ael}</p>
<!-- <p>{document->titre}</p> -->
<!--
<table {sEncadrer} align="center" class="titre">
<tr>
    <td>
        {sTitre}
    </td>
</tr>
</table>
-->
[BLOCK_EVAL_ETAT+]
<div id="Eval">
<h3>{Eval_Globale}</h3>
<p>{txt_eval}</p>
</div>
<div id="Etat">
<h3>Etat de l'activité : </h3>
{txt_etat}
</div>
[BLOCK_EVAL_ETAT-]
<form name="questionnaire" action="formulaire.php?idActiv={url_idActiv}&idSousActiv={url_idSousActiv}#FormulaireInline" method="post" enctype="text/html" id="form_Formulaire_Inline">
[BLOCK_FORMULAIRE_MODIFIER+]
<input type="hidden" name="idFormulaire" value="{iIdFormulaire}" />
{input_ss_activ}
{ListeObjetFormul}
</form>

<p class="valider_form_inline">
{bouton_valider}
</p>
</div>
[BLOCK_FORMULAIRE_MODIFIER-]
[BLOCK_FORM_INLINE-]

<p>&nbsp;</p>
</body>
</html>

[SET_LISTE_ICONES+]
[ARRAY_LISTE_ICONES+]
<a href="{a.exporter.href}" class="exporter_donnees" onfocus="blur()" target="_self" title="Exporter les données"><img src="commun://icones/exporter-formulaire.gif" width="24" height="24" border="0"></a>&nbsp;#@#
<a href="javascript: void(0);" onclick="{a.choix_courriel.href}; return false;" onfocus="blur()" title="Cliquer ici pour envoyer un courriel"><img src="commun://icones/24x24/courriel_envoye.gif" width="24" height="24" border="0"></a>&nbsp;
[ARRAY_LISTE_ICONES-]
<p style="width: 100%; height: 25px; text-align: right;">{liste_icones}</p>
[SET_LISTE_ICONES-]

[SET_DESCRIPTION+]
<p class="description_zdc">{description->texte}</p>
[SET_DESCRIPTION-]

[SET_PAS_ACTIVITE_REALISEE+]
<p style="text-align: center; font-size: 7pt;">Vous n'avez pas encore r&eacute;alis&eacute; d'activit&eacute;. {activite->params}</p>
[SET_PAS_ACTIVITE_REALISEE-]

[SET_DOCUMENT_DE_BASE+]
[VAR_TITRE+]Pour commencer[VAR_TITRE-]
[VAR_DOCUMENT_URL+]
<img src="commun://espacer.gif" width="15" height="1" border="0">
<a class="formulaire_base" href="{a->href}" onclick="{a->onclick}" onfocus="blur()" target="{a->target}">{a->label}</a>
[VAR_DOCUMENT_URL-]
[VAR_CONSIGNE+]<span class="formulaire_consigne">D&eacute;marrer une activit&eacute;&nbsp;? Cliquez sur l'intitul&eacute; rouge</span>[VAR_CONSIGNE-]
{document_de_base}
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
<td><img src="commun://espacer.gif" width="10" height="1" border="0"></td>
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
<p style="display:block; color:#485EA5;font-size:8pt;">{titreTravauxEnCours}</p>
<table border="0" cellspacing="0" cellpadding="0" width="90%">
<tr><td>{onglet}</td></tr>
</table>
<!-- ]]-->
[SET_TRAVAUX_EN_COURS-]


[SET_TRAVAUX_SOUMIS+]
<!--[[ Travaux soumis -->
[VAR_TITRE+]Travaux soumis pour &eacute;valuation[VAR_TITRE-]
[VAR_LISTE_DOCUMENTS+]
<ul>{liste_documents}</ul>
<p style="text-align:right;margin-top:0;">{evaluer->bouton}</p>
[VAR_LISTE_DOCUMENTS-]
[VAR_BOUTON_EVALUER+]
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Obtenir l'&eacute;valuation</a>###
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Evaluer</a>###
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Obtenir le commentaire</a>###
<a id="id_soumettre_{personne->id}" class="soumettre_passif" href="javascript: void(0);" onclick="sauverPosYPage(); return formulaire_eval('','winFormulaireEval')" onfocus="blur()">Commenter</a>
[VAR_BOUTON_EVALUER-]
[VAR_BOUTON_SELECTIONNER_FORMULAIRE+]
<input style="vertical-align:bottom;margin:0" type="radio" name="{radio->name}" onclick="surbrillance('id_soumettre_{personne->id}')" value="{radio->value}" onfocus="blur()">
[VAR_BOUTON_SELECTIONNER_FORMULAIRE-]
[VAR_LIGNE_DOCUMENT+]
<li style="list-style-type:none; margin:8px 0 0;">
    <span style="display:inline-block;width:20px;">{document->selectionner}</span>
    <span style="display:inline-block;vertical-align:top;width:275px;">
        <a href="{a->href}" onclick="{a->onclick}" onfocus="blur()" target="{a->target}"><b>Version&nbsp;{document->titre}</b> {document->fini} {document->date}</a>
    </span>
    <span style="display:inline-block;font-size: 8pt;vertical-align:top;width:200px;">{document->evalue}</span>
    <span style="display:inline-block;font-size: 8pt;">{evaluer->bouton}</span>
</li>
[VAR_LIGNE_DOCUMENT-]
[VAR_PAS_DOCUMENT_TROUVE+]
<p style="text-align: center; font-size: 7pt;">Pas de document trouv&eacute;</p>
[VAR_PAS_DOCUMENT_TROUVE-]
[VAR_CONSIGNE+]
 - Acc&eacute;der &agrave; l'&eacute;valuation&nbsp;? &gt;&gt; S&eacute;lectionnez la version et cliquez sur &laquo;&nbsp;Obtenir l'&eacute;valuation&nbsp;&raquo;###
 - Evaluer&nbsp;? &gt;&gt; S&eacute;lectionner une version et cliquez sur &laquo;&nbsp;Evaluer&nbsp;&raquo;
[VAR_CONSIGNE-]
[VAR_CONSIGNE_GLOBALE+]
D&eacute;marrer une activit&eacute;&nbsp;? &gt;&gt; Cliquez sur l'intitul&eacute; rouge
[VAR_CONSIGNE_GLOBALE-]
[VAR_FORMULAIRE_EVALUATION+]
###
###
###
&nbsp;&nbsp;<img src="theme://formulaire/res_non_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(non &eacute;valu&eacute;)###
&nbsp;&nbsp;<img src="theme://formulaire/res_a_poursuivre.gif" width="8" height="8" border="0">&nbsp;&nbsp;(&eacute;valu&eacute;&nbsp;: &agrave; poursuivre)###
&nbsp;&nbsp;<img src="theme://formulaire/res_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(&eacute;valu&eacute;&nbsp;: activit&eacute; termin&eacute;e)###
###
###
&nbsp;&nbsp;<img src="theme://formulaire/res_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(activit&eacute; termin&eacute;e)###
&nbsp;&nbsp;<img src="theme://formulaire/res_eval.gif" width="8" height="8" border="0">&nbsp;&nbsp;(comment&eacute; : activit&eacute; termin&eacute;e)
[VAR_FORMULAIRE_EVALUATION-]
<!-- <hr class="hr1"> -->
<!-- <p style="color:#485EA5;font-size:8pt;">{titreTravauxFinis}</p> -->
<div style="">{onglet}<span class="formulaire_consigne">{consigne}</span></div>
<!-- Travaux soumis ]]-->
[SET_TRAVAUX_SOUMIS-]

