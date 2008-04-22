<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://collecticiel.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript" src="collecticiel.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_sSurbrillance = null;
var g_oDomWindow;

var asTextesBarreIcones = Array();
asTextesBarreIcones["courriel"] = "Envoyer un courriel";
asTextesBarreIcones["profil"] = "Profil de l'&eacute;tudiant";
asTextesBarreIcones["equipes"] = "Afficher la liste des membres de cette &eacute;quipe";
asTextesBarreIcones["ressource_deposer"] = "Cliquer ici pour d&eacute;poser votre document dans ce collecticiel";
asTextesBarreIcones["ressource_vote"] = "Cliquer ici pour soumettre le document s&eacute;lectionn&eacute; &agrave; votre tuteur";
asTextesBarreIcones["ressource_supprimer"] = "<span style='color: rgb(136,37,37); font-size: 7pt;'>Supprimer le(s) document(s) de ce collecticiel</span>";
asTextesBarreIcones["ressource_telecharger"] = "Cliquer ici si vous d&eacute;sirez t&eacute;l&eacute;charger ce document";
asTextesBarreIcones["ressource_evaluation"] = "Cliquer ici pour ouvrir la fen&ecirc;tre de l'&eacute;valuation";

function page_y_offset_on_scroll()
{
	if (oFrameFiltres() != null)
		oFrameFiltres().elements["pageYOffset"].value = g_oDomWindow.pageYOffset();
	setTimeout("page_y_offset_on_scroll()",1000);
}

function init()
{
	g_oDomWindow = new DOMWindow(self);
	
	if (oFrameFiltres() != null)
		g_oDomWindow.scrollTo(0,oFrameFiltres().elements["pageYOffset"].value);
	
	page_y_offset_on_scroll();
}
//-->
</script>
</head>
<body onload="init()">
[BLOCK_CONSIGNE+]
<div id="consigne">{consigne}</div>
[BLOCK_CONSIGNE-]

[BLOCK_FICHIER_DE_BASE+]
<div id="fichier_de_base">
[BLOCK_ICONE_DOCUMENT_TELECHARGER+]<div style="float: left;">&nbsp;<img src="theme://icones/disquette.gif" border="0" style="vertical-align: middle;">&nbsp;&nbsp;<a href="{fichier_de_base.href}" onfocus="blur()">{fichier_de_base.label}</a></div><p>&nbsp;</p>[BLOCK_ICONE_DOCUMENT_TELECHARGER-]
[BLOCK_ICONE_TRANSFERER_DOCUMENTS+]<div style="float: right;">&nbsp;<a href="javascript: void(0);" onclick="return ressource_transfert({transfert_fichiers.paramsUrl});"><img src="commun://icones/24x24/transfert_fichiers.gif" width="24" height="24" title="Transf&eacute;rer des documents" border="0"></a>&nbsp;</div>[BLOCK_ICONE_TRANSFERER_DOCUMENTS-]
[BLOCK_ICONE_COURRIEL+]<div style="float: right;">&nbsp;{fichier_de_base.courriel}&nbsp;</div>[BLOCK_ICONE_COURRIEL-]
</div>
[BLOCK_FICHIER_DE_BASE-]

[BLOCK_COLLECTICIEL+]
<p>&nbsp;</p>
[SET_COLLECTICIEL_NOTE+]<a class="texte_associe" href="javascript: void(0);" onclick="return ressource_description('{document.id}')" onfocus="blur()"><img src="commun://icones/16x16/collecticiel-note.gif" width="16" height="16" border="0"></a>[SET_COLLECTICIEL_NOTE-]
[ARRAY_COLLECTICIEL_SELECTIONNER+]
&nbsp;-&nbsp;###
<input type="checkbox" name="idResSA{collecticiel.id}" onclick="select_deselect_checkbox(this)" onfocus="blur()">###
<input type="radio" name="idResSA{collecticiel.id}[]" value="{document.id}" onclick="verif_checkbox_principal(this)" onfocus="blur()">###
<input type="checkbox" name="idResSA{collecticiel.id}[]" value="{document.id}" onclick="verif_checkbox_principal(this)" onfocus="blur()">
[ARRAY_COLLECTICIEL_SELECTIONNER-]
[SET_DOCUMENTS+]
<form name="formCollecticiel{collecticiel.id}">
[BLOCK_BARRE_OUTILS+]
[SET_SEPARATEUR_ICONES+]<td class="barre_icones" style="width: 1%;">&nbsp;|&nbsp;</td>[SET_SEPARATEUR_ICONES-]
[ARRAY_BARRE_OUTILS+]
<td class="barre_icones" style="width: 1%;" onmouseover="this.className='cellule_icone_surbrillante'" onmouseout="this.className='barre_icones'"><a href="javascript: void(0);" onclick="return profil('?idPers={personne.id}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','profil')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')"><img src="commun://icones/16x16/profil.gif" width="16" height="16" border="0"></a></td>
###<td class="barre_icones" style="width: 1%;" onmouseover="this.className='cellule_icone_surbrillante'" onmouseout="this.className='barre_icones'"><a href="javascript: void(0);" onclick="return liste_equipes('{equipe.id}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','equipes')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')"><img src="commun://icones/16x16/equipe.gif" width="16" height="16" border="0"></a></td>
###<td class="barre_icones" style="width: 1%;" onmouseover="this.className='cellule_icone_surbrillante'" onmouseout="this.className='barre_icones'"><a href="javascript: void(0);" onclick="return choix_courriel('?typeCourriel=courriel-collecticiel@collecticiel{courriel.modalite}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','courriel')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')"><img src="commun://icones/mail.gif" width="16" height="16" border="0"></a></td>
[ARRAY_BARRE_OUTILS-]
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>{barre_outils}<td class="barre_icones">&nbsp;</td></tr>
</table>
[BLOCK_BARRE_OUTILS-]
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td class="cellule_sous_titre">&nbsp;Titre&nbsp;{entete.tri_titre}</td>
<td class="cellule_sous_titre">&nbsp;D&eacute;pos&eacute; par&nbsp;{entete.tri_auteur}</td>
<td class="cellule_sous_titre">&nbsp;Date&nbsp;{entete.tri_date}</td>
<td class="cellule_sous_titre">&nbsp;Etat&nbsp;{entete.tri_statut}</td>
<td class="cellule_sous_titre" style="width: 1%; white-space: nowrap;">&nbsp;Evalu&eacute;&nbsp;{entete.tri_evalue}</td>
<td class="cellule_sous_titre" style="width: 1%;">Texte<br>associ&eacute;</td>
<td class="cellule_sous_titre" style="width: 1%;">{entete.selection}</td>
</tr>
[BLOCK_DOCUMENT+]
<tr onmouseover="table_ligne_surbrillance(this,true)" onmouseout="table_ligne_surbrillance(this,false)">
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="titre">&nbsp;<a href="{a.titre.href}" class="titre" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_telecharger')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')" onfocus="blur()">{document.titre}</a>&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="auteur">&nbsp;{document.auteur}&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="date" title="{document.heure}">&nbsp;{document.date}&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="statut">&nbsp;{document.statut}&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="evalue">&nbsp;{document.evalue}&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]"><div class="texte_associe">&nbsp;{document.texte_associe}&nbsp;</div></td>
<td class="[CYCLE:cellule_clair|cellule_fonce]" style="text-align: center;">{document.selection}</td>
</tr>
[BLOCK_DOCUMENT-]
[BLOCK_SANS_DOCUMENTS+]
<tr><td colspan="7" class="cellule_clair"><div class="sans_document">Pas de document trouv&eacute;</div></td></tr>
[BLOCK_SANS_DOCUMENTS-]
</table>
[BLOCK_GESTION_DOCUMENTS+]
[VAR_SUPPRIMER+]<a href="javascript: void(0);" onclick="return ressource_supprimer('{collecticiel.id}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_supprimer')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')">Supprimer</a>[VAR_SUPPRIMER-]
[VAR_DEPOSER+]<a href="javascript: void(0);" onclick="return ressource_deposer()" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_deposer')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')">D&eacute;poser</a>[VAR_DEPOSER-]
[VAR_SOUMETTRE_POUR_EVALUATION+]&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="return ressource_vote('{collecticiel.id}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_vote')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')">Soumettre pour &eacute;valuation</a>[VAR_SOUMETTRE_POUR_EVALUATION-]
[VAR_VOTER_POUR_SOUMETTRE+]&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="return ressource_vote('{collecticiel.id}')" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_vote')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')">Voter pour soumettre</a>[VAR_VOTER_POUR_SOUMETTRE-]
<div class="gestion_documents_gauche">[BLOCK_DEPOSER+]D&eacute;poser[BLOCK_DEPOSER-]</div>
<div class="gestion_documents_droite">[BLOCK_SUPPRIMER+]Supprimer[BLOCK_SUPPRIMER-][BLOCK_SOUMETTRE_POUR_EVALUATION+]&nbsp;|&nbsp;Soumettre pour &eacute;valuation[BLOCK_SOUMETTRE_POUR_EVALUATION-][BLOCK_VOTER_POUR_SOUMETTRE+]&nbsp;|&nbsp;Voter pour soumettre[BLOCK_VOTER_POUR_SOUMETTRE-]</div>
<div class="spacer"></div>
[BLOCK_GESTION_DOCUMENTS-]
<input type="hidden" name="nom" value="idResSA{collecticiel.id}">
</form>
<p style="border: rgb(210,210,210) none 1px; border-top-style: dotted; color: rgb(88,117,159); font-size: 7pt;">&#8250;&nbsp;<span id="id_aide_en_ligne_{collecticiel.id}" style="color: rgb(88,117,159); font-size: 7pt;">&nbsp;</span></p>
[SET_DOCUMENTS-]
{documents}
[BLOCK_COLLECTICIEL-]
[BLOCK_SANS_COLLECTICIEL+]<div id="sans_collecticiel">Pas de document trouv&eacute;</div>[BLOCK_SANS_COLLECTICIEL-]
<p>&nbsp;</p>
</body>
</html>
[SET_CHOISIR_TRI+]
<img src="commun://choisir_tri.gif" with="7" height="9" alt="" border="0" usemap="#{html.img.usemap}">&nbsp;
<map name="{html.img.usemap}">
<area href="javascript: void(0);" onclick="return inverser_type_tri('{html.area.tri}','{html.area.type_tri.croissant}')" shape="rect" coords="0,0,7,4" title="Tri croissant">
<area href="javascript: void(0);" onclick="return inverser_type_tri('{html.area.tri}','{html.area.type_tri.decroissant}')" shape="rect" coords="0,5,7,9" title="Tri d&eacute;croissant">
</map>
[SET_CHOISIR_TRI-]
[SET_TRI_CROISSANT+]<a href="javascript: void(0);" onclick="return inverser_type_tri('{html.a.tri}','{html.a.type_tri}')"><img src="theme://sort-incr.gif" border="0"></a>[SET_TRI_CROISSANT-]
[SET_TRI_DECROISSANT+]<a href="javascript: void(0);" onclick="return inverser_type_tri('{html.a.tri}','{html.a.type_tri}')"><img src="theme://sort-desc.gif" border="0"></a>[SET_TRI_DECROISSANT-]

[SET_LISTE_VOTANTS+]<a href="javascript: void(0);" onclick="return ressource_votants('{document.id}','{equipe.id}')" onfocus="blur()">{document.statut}</a>[SET_LISTE_VOTANTS-]

[SET_RESSOURCE_EN_COURS+]<span class="en_cours">Non</span>[SET_RESSOURCE_EN_COURS-]
[SET_RESSOURCE_SOUMISE+]Non|<a href="javascript: void(0);" onclick="return ressource_evaluation('{document.id}')" onfocus="blur()" class="evalue">Non</a>[SET_RESSOURCE_SOUMISE-]
[SET_RESSOURCE_EVALUATION+]<a href="javascript: void(0);" onmouseover="aide_en_ligne(this,'{collecticiel.id}','ressource_evaluation')" onmouseout="aide_en_ligne(this,'{collecticiel.id}')" onclick="return ressource_evaluation('{document.id}')" onfocus="blur()" class="evalue">Oui</a>[SET_RESSOURCE_EVALUATION-]

