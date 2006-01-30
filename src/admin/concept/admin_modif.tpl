<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="/ipfhainaut_dev/themes/ipfhainaut/globals.css">
<link type="text/css" rel="stylesheet" href="/ipfhainaut_dev/themes/ipfhainaut/onglet.css">
<link type="text/css" rel="stylesheet" href="/ipfhainaut_dev/themes/ipfhainaut/concept.css">
<script type="text/javascript" language="javascript" src="/ipfhainaut_dev/js/window.js"></script>
<script type="text/javascript" language="javascript" src="admin_modif.js"></script>
<script type="text/javascript" language="javascript">
<!--

var g_iIdInterval=null;

function DeposerFichiers()
{
	var wForm;
	var sUrl     = "deposer_fichiers.php?idf=2&idsa=31&ttp=D%E9poser%20des%20fichiers%20relatifs%20%E0%20l%27%E9l%E9ment%20actif";
	var iLargeur = 470;
	var iHauteur = 190;
	var iGauche  = (screen.width-iLargeur)/2;
	var iHaut    = (screen.height-iHauteur)/2;
	var sFeatures = "left=" + iGauche
		+ ",top=" + iHaut
		+ ",width=" + iLargeur
		+ ",height=" + iHauteur
		+ ",location=0,menubar=0,scrollbars=0,resizable=0,status=0,toolbar=0";
	
	wForm = window.open(sUrl,"WIN_DEPOSER_FICHIERS",sFeatures);
	wForm.focus();
}

function RecupererFichiers()
{
	var wForm;
	
	var iLargeurFenetre = 400;
	var iHauteurFenetre = 500;
	var iPositionGauche = (screen.width-iLargeurFenetre)/2;
	var iPositionHaut   = (screen.height-iHauteurFenetre)/2;
	
	var sUrl = "recuperer_fichiers.php?FORM=2&ACTIV=31";
	var sFeatures = "top=" + iPositionHaut  + ",left=" + iPositionGauche + ",width=" + iLargeurFenetre + ",height=" + iHauteurFenetre + ",location=0,menubar=0,scrollbars=0,resizable=0,status=0,toolbar=0";
	
	wForm = window.open(sUrl,"WIN_RECUPERER_FICHIERS",sFeatures);
	wForm.focus();
}

function menu()
{
	var obj = document.getElementById("id_menu_sous_activ").style;
	var iPosMenu;
	
	if (typeof(window.innerHeight) != "undefined")
		iPosMenu = window.pageYOffset + window.innerHeight;
	else if (typeof(document.body.clientHeight) != "undefined")
		iPosMenu = document.body.scrollTop + document.body.clientHeight;

	obj.top = iPosMenu - 17;
}

function init()
{
	if (top.frames["Titre"] && top.frames["Titre"].changerSousTitre)
		top.frames["Titre"].changerSousTitre("%3Cb%3E%26Eacute%3Bl%26eacute%3Bment%20actif%3C%2Fb%3E%26nbsp%3B%26raquo%3B%26nbsp%3BGlossaire");
	
	top.frames["AdminModifMenu"].location = "admin_modif_menu.php?type=6&params=2:3:22:0:31:98";
}

//-->
</script>
</head>
<body class="modif" onload="init()">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
<td class="admin_modif_menu">
&nbsp;</td>
<td class="admin_modif_menu"  width="1%" nowrap="1"><a href="javascript: DeposerFichiers();">Déposer sur le serveur</a>&nbsp;&#8226;&nbsp;<a href="javascript: RecupererFichiers();">Récupérer du serveur</a>
</td>
</tr>
</table>
<br>
<form action="/ipfhainaut_dev/admin/concept/admin_modif.php" method="post">

<table border="0" cellspacing="0" cellpadding="5" width="100%">
[BLOCK_ELEMENTS_FORMULAIRE+][BLOCK_ELEMENTS_FORMULAIRE-]
<!-- Statut -->

<tr>
<td><div class="intitule">Statut&nbsp;:</div></td>
<td>
<select name="STATUT">
	<option value="1">Fermé</option>
	<option value="2">Ouvert</option>
	<option value="3">Invisible</option>
	<option value="6" selected>Même statut que le bloc</option>
</select>
</td>
</tr>

<!-- Date de départ et date de fin -->
<!--
<tr>
<td align="right" style="text-align: right; white-space: no;" nowrap>Période de validité :</td>
<td>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><input type="text" name="dateDeb" size="10" maxlength="10" value="08-06-2004"></td>
<td style="padding-left: 15px; padding-right: 15px;">-</td>
<td><input type="text" name="dateFin" size="10" maxlength="10" value="08-06-2004"></td>
</tr>
</table>
</td>
</tr
//-->

<!-- Type -->
<tr><td>&nbsp;</td><td><hr></td></tr>
<tr>
<td class="intitule">Type&nbsp;:</td>
<td>
<select name="TYPE" onchange="choisirType(aoType,this.value)">
	<option value="0">Choisissez un type pour cet &eacute;l&eacute;ment actif</option>
	<option value="1" selected>Affichage du serveur (html, doc, ppt, gif, swf, pdf,...)</option>
	<option value="2">Document à télécharger</option>
	<option value="3">Lien vers un site Internet</option>
	<option value="7">Collecticiel</option>
	<option value="6">Galerie</option>
	<option value="4">Chat</option>
	<option value="5">Forum</option>

</select>

</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>

<!-- :DEBUT: Affichage d'une page HTML (du serveur) -->

<div id="lien_page_html" class="Cacher">

<table border="1" cellspacing="0" cellpadding="2" width="100%">

<tr>
<td width="200"><div class="intitule">Choisir l'index&nbsp;:</div></td>

<td>
<select name="DONNEES[1]">
<option value="">Pas de fichier actuellement</option>
<option value="Glossaire Finances provinciales.htm" style="background-color: #FFFFCC;" selected>Glossaire Finances provinciales.htm</option>
<option value="questionnaire avis d'équipe.htm">questionnaire avis d'équipe.htm</option>
</select>

</td>
</tr>
<!-- Position ancien checkbox Première page -->
<tr>
<td class="intitule"><input type="checkbox" name="PREMIERE_PAGE[1]" onfocus="blur()"></td>
<td>Premi&egrave;re page&nbsp;<img src="/ipfhainaut_dev/themes/ipfhainaut/icones/etoile.gif" border="0"></td>
</tr>

<tr>
<td class="intitule">Modalit&eacute; d'affichage&nbsp;:</td>
<td>
<select name="MODALITE_AFFICHAGE[1]" onchange="MontrerCacher('div_description',this.selectedIndex)">
<option value="1" selected>Frame centrale (direct)</option>
<option value="2">Frame centrale (indirect)</option>
<option value="3">Nouvelle fenêtre (direct)</option>
<option value="4">Nouvelle fenêtre (indirect)</option>
</select>
</td>
</tr>

</table>

</div>

<!-- :FIN: Affichage d'une page HTML (du serveur) -->

<!-- :DEBUT: Document à télécharger -->

<div id="lien_document_telecharger" class="Cacher">
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="intitule" width="200">Choisir le document&nbsp;:</td>

<td>
<select name="DONNEES[2]">
<option value="">Pas de fichier actuellement</option>
<option value="Glossaire Finances provinciales.htm" style="background-color: #FFFFCC;" selected>Glossaire Finances provinciales.htm</option>
<option value="questionnaire avis d'équipe.htm">questionnaire avis d'équipe.htm</option>
</select>
[&nbsp;<a href="javascript: DeposerFichiers();">D&eacute;poser</a>&nbsp;]</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<!-- Modalité d'affichage -->
<tr>
<td class="intitule">Modalit&eacute; d'affichage&nbsp;:</td>
<td>
<select name="MODALITE_AFFICHAGE[2]"  onchange="javascript: MontrerCacher('div_description',this.selectedIndex);" >
<option value="1">Direct (téléchargement immédiat)</option>
<option value="2">Indirect (téléchargement en deux temps)</option>
</select>
</td>
</tr>
</table>
</div>

<!-- :FIN: Document à télécharger -->

<!-- :DEBUT: Lien vers un site Internet -->

<div id="lien_site_internet" class="Cacher">
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
<td class="intitule" width="200">http://</td>
<td><input type="text" name="DONNEES[3]" value="Glossaire Finances provinciales.htm" size="50"></td>
</tr>

<tr>
<td class="intitule">Modalit&eacute; d'affichage&nbsp;:</td>
<td>
<select name="MODALITE_AFFICHAGE[3]" onchange="MontrerCacher('div_description',this.selectedIndex)">
<option value="1" selected>Frame centrale (direct)</option>
<option value="2">Frame centrale (indirect)</option>
<option value="3">Nouvelle fenêtre (direct)</option>
<option value="4">Nouvelle fenêtre (indirect)</option>
</select>
</td>
</tr>

</table>
</div>

<!-- :FIN: Lien vers un site Internet -->

<!-- :DEBUT: Collecticiel (7) -->

<div id="lien_collecticiel" class="Cacher">

<table border="0" cellspacing="4" cellpadding="0">

<tr>
<td><div class="intitule">Modalit&eacute;&nbsp;:</div></td>
<td><select name="MODALITE[7]">
<option value="0" selected>&nbsp;même modalité que le bloc&nbsp;</option>
</select>
</td>
</tr>

<tr>
<td><div class="intitule">Fichier de base&nbsp;:</div></td>

<td>
<select name="DONNEES[7]" >
<option value="">Pas de fichier actuellement</option>
<option value="Glossaire Finances provinciales.htm" style="background-color: #FFFFCC;" selected>Glossaire Finances provinciales.htm</option>
<option value="questionnaire avis d'équipe.htm">questionnaire avis d'équipe.htm</option>
</select>
[&nbsp;<a href="javascript: DeposerFichiers();">D&eacute;poser</a>&nbsp;]</td>
</tr>
<tr>
<td><div class="intitule">Intitulé&nbsp;du&nbsp;lien&nbsp;:</div></td>
<td><input type="text" size="50" name="INTITULE[7]" value="Fichier de base &agrave; t&eacute;l&eacute;charger"></td>
</tr>

<!-- Description -->

<tr>
<td class="intitule">Consignes&nbsp;:</td>
<td><textarea name="DESCRIPTION[7]" cols="55" rows="3"></textarea></td>
</tr>

<tr><td class="intitule"><input type="checkbox" name="PREMIERE_PAGE[7]"></td><td>Premi&egrave;re page&nbsp;<img src="/ipfhainaut_dev/themes/ipfhainaut/icones/etoile.gif" border="0"></td></tr></table>

</div>

<!-- :FIN: Collecticiel -->

<!-- :DEBUT: Chat -->
<div id="lien_chat" class="cacher">
<img src="/ipfhainaut_dev/themes/ipfhainaut/signet-1.gif" border="0">&nbsp;<a href="javascript: composerChats('98'); top.frames['ADMINFRAMEMODIF'].document.forms[0].submit();">Cliquez ici, si vous désirez composer ou modifier vos &laquo;&nbsp;chat&nbsp;&raquo;</a></div>
<!-- :FIN: Chat -->


<!-- :DEBUT: Description des intitulés -->

<div id="div_description" class="Cacher">
<br>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td class="intitule" width="200">Intitulé&nbsp;du&nbsp;lien&nbsp;:</td>
<td><input type="text" size="50" name="INTITULE[0]" value=""></td>
</tr>

<!-- Description -->

<tr>
<td class="intitule">Description&nbsp;de&nbsp;l'intitulé&nbsp;:</td>
<td><textarea name="DESCRIPTION[0]" cols="55" rows="3"></textarea></td>
</tr>

</table>
</div>

<!-- :FIN: Description des intitulés -->
<!-- :DEBUT: Forum --><div id="lien_forum" class="Cacher"><table border="0" cellspacing="0" cellpadding="2" width="100%"><tr><td><div class="intitule">Modalit&eacute;&nbsp;:</div></td><td><select name="MODALITE[5]"><option value="0" selected>Même modalité que le parent</option><option value="3">Pour tous</option><option value="2">Par équipe</option></select></td></tr>
<tr><td class="intitule"><input type="checkbox" name="ACCESSIBLE_VISITEURS[5]" checked></td><td>J'autorise les visiteurs &agrave; consulter les messages de ce forum</td></tr>
</table>
</div>
<!-- :FIN: Forum -->
<!-- :DEBUT: Galerie --><div id="lien_galerie" class="Cacher"><table border="0" cellspacing="0" cellpadding="2" width="100%"><tr><td class="intitule" width="1%">Consigne&nbsp;:</td><td><textarea rows="5" cols="50" name="DESCRIPTION[6]"></textarea></td></tr><tr><td class="intitule" width="1%" nowrap="1">Collecticiels associ&eacute;s&nbsp;:</td><td>Pas de collecticiel trouvé<br><br></td></tr><tr><td class="intitule"><input type="checkbox" name="PREMIERE_PAGE[6]"></td><td>Premi&egrave;re page&nbsp;<img src="/ipfhainaut_dev/themes/ipfhainaut/icones/etoile.gif" border="0"></td></tr></table></div><!-- :FIN: Galerie -->

<hr>

</td>

</tr>


</table>
<br>
<script language="javascript" type="text/javascript">
<!--

var aoType = new Array();

if (document.getElementById)
{
	// aoType[num]: num = Select.value - 1
	aoType[0] = document.getElementById("lien_page_html");
	aoType[1] = document.getElementById("lien_document_telecharger");
	aoType[2] = document.getElementById("lien_site_internet");
	aoType[3] = document.getElementById("lien_chat");
	aoType[4] = document.getElementById("lien_forum");
	aoType[5] = document.getElementById("lien_galerie");
	aoType[6] = document.getElementById("lien_collecticiel");
}

choisirType(aoType,"1");

//-->
</script>
<input type="hidden" name="act" value="modifier">
<input type="hidden" name="type" value="6">
<input type="hidden" name="params" value="2:3:22:0:31:98">
</form>
</body>
</html>
[SET_NUMERO_ORDRE+]
<!-- Numéro d'ordre -->
<tr>
<td nowrap="nowrap" width="1%"><div  class="intitule">Num&eacute;ro d'ordre&nbsp;:</div></td>
<td>
<select name="ORDRE">
[BLOCK_OPTION_NUMERO_ORDRE+]<option value="{option->value}"{option->selected}>&nbsp;&nbsp;{option->label}&nbsp;&nbsp;</option>[BLOCK_OPTION_NUMERO_ORDRE-]
</select>
</td>
</tr>
[SET_NUMERO_ORDRE-]
[SET_NOM+]
<!-- Nom -->
<tr><td><div class="intitule">Nom&nbsp;:</div></td><td><input type="text" name="NOM" size="53" value="{input->value}"></td></tr>
[SET_NOM-]
[SET_INFO_BULLE+]
<!-- Info bulle -->
<tr><td><div class="intitule">Info bulle&nbsp;:</div></td><td><input type="text" name="INFOBULLE" size="53" maxlength="128" value="{input->value}"></td></tr>
[SET_INFO_BULLE-]
