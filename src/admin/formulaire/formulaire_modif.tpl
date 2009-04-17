<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="css://commun/globals.css" />
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css" />
<title>Modification des activités en ligne</title>
<script src="selectionobj.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
// tableau qui contiendra les objets SELECT permettant de modifier l'ordre
// des propositions dans les questions de type Liste/Radios/Cases
var g_aoSelects = new Array();

// initialiser le tableau de SELECT en fonction du nom
function init()
{
	var i, j;
	var sNomSelectOrdre = 'selOrdreProposition';
	var iTailleNomSelectOrdre = sNomSelectOrdre.length;
	
	aoSelects = document.getElementsByTagName('select');
	for (i = 0, j = 0; i < aoSelects.length; i++)
	{
		if (aoSelects[i].name.substr(0, iTailleNomSelectOrdre) == sNomSelectOrdre)
		{
			g_aoSelects[j] = aoSelects[i];
			g_aoSelects[j].onchange = changerOrdreProposition;
			g_aoSelects[j].selectedIndexCopy = g_aoSelects[j].selectedIndex;
			j++;
		}
	}
}

// quand l'ordre d'une proposition est modifié, il faut s'assurer que la nouvelle 
// valeur n'existe pas pour une autre proposition (auquel cas on lui attribue l'ancienne 
// (valeur de la position qui vient d'être modifiée)
function changerOrdreProposition()
{
	var i;
	
	for (i = 0; i < g_aoSelects.length; i++)
	{
		if (g_aoSelects[i] != this && g_aoSelects[i].selectedIndex == this.selectedIndex)
		{
			g_aoSelects[i].selectedIndex = this.selectedIndexCopy;
			g_aoSelects[i].selectedIndexCopy = this.selectedIndexCopy;
			break;
		}
	}
	
	this.selectedIndexCopy = this.selectedIndex;
}


function soumettre(TypeAct,Parametre)
{
	document.forms['formmodif'].typeaction.value=TypeAct;
	
	if (TypeAct == 'supprimer')
		document.forms['formmodif'].parametre.value=Parametre;
	else
		document.forms['formmodif'].parametre.value="";
	
	document.forms['formmodif'].submit();
}
//-->
</script>
</head>
<body onload="init()" class="formulaire_modif">
<div id="entete">
	<h3>{Titre_page}</h3>
</div>
<div id="conteneur">
[BLOCK_MODIF_TXTLONG+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">{sMessageErreur1} Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQTL}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<ul>
		<li>
			<label for="idlargeur">{sMessageErreur2} Largeur de la boîte de texte :</label>
			<input type="text" size="3" maxlength="10" name="Largeur" id="idlargeur" value="{LargeurQTL}" onblur="verifNumeric(this)" />
		</li>
		<li>
			<label for="idhauteur">{sMessageErreur3} Hauteur de la boîte de texte :</label>
			<input type="text" size="3" maxlength="10" name="Hauteur" id="idhauteur" value="{HauteurQTL}" onblur="verifNumeric(this)" />
		</li>
	</ul>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_TXTLONG-]
[BLOCK_MODIF_TXTCOURT+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQTC}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<table border="0" cellpadding="1" cellspacing="10">
	<tr>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAv">{TxtAvQTC}</textarea></td>
		<td id="bloczonerep">Zone<br />réponse</td>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAp">{TxtApQTC}</textarea></td>
	</tr>
	</table>
	<ul>
		<li>
			<label for="idlargeur">{sMessageErreur1} Taille de la boîte de texte :</label>
			<input type="text" size="3" maxlength="3" name="Largeur" id="idlargeur" value="{LargeurQTC}" onblur="verifNumeric(this)" />
		</li>
		<li>
			<label for="idmaxcar">{sMessageErreur2} Nombre de caractères maximum :</label>
			<input type="text" size="3" maxlength="3" name="MaxCar" id="idmaxcar" value="{MaxCarQTC}" onblur="verifNumeric(this)" />
		</li>
	</ul>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_TXTCOURT-]
[BLOCK_MODIF_NOMBRE+]
	<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQN}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<table border="0" cellpadding="1" cellspacing="10">
	<tr>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAv">{TxtAvQN}</textarea></td>
		<td id="bloczonerep">Zone<br />réponse</td>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAp">{TxtApQN}</textarea></td>
	</tr>
	</table>
	<ul>
		<li>
			<label for="idnbmin">{sMessageErreur1} Nombre minimum :</label>
			<input type="text" size="10" maxlength="9" name="NbMin" id="idnbmin" value="{NbMinQN}" onblur="verifNumeric(this)" />
		</li>
		<li>
			<label for="idnbmax">{sMessageErreur2} Nombre maximum :</label>
			<input type="text" size="10" maxlength="10" name="NbMax" id="idnbmax" value="{NbMaxQN}" onblur="verifNumeric(this)" />
		</li>
		<li>
			<label for="idmultix">{sMessageErreur3} Coefficient multiplicateur :</label>
			<input type="text" size="5" maxlength="10" name="Multi" id="idmultix" value="{MultiQN}" onblur="verifNumeric(this)" />
		</li>
	</ul>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_NOMBRE-]
[BLOCK_MODIF_LISTEDER+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQLD}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<table border="0" cellpadding="1" cellspacing="10">
	<tr>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAv">{TxtAvQLD}</textarea></td>
		<td id="bloczonerep">Zone<br />réponse</td>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAp">{TxtApQLD}</textarea></td>
	</tr>
	</table>
	</fieldset>
	<fieldset id="zoneproposition">
	<legend>Propositions</legend>
	{RetourReponseQLDModif}
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_LISTEDER-]
[BLOCK_MODIF_RADIO+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQR}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<table border="0" cellpadding="1" cellspacing="10">
	<tr>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAv">{TxtAvQR}</textarea></td>
		<td id="bloczonerep">Zone<br />réponse</td>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAp">{TxtApQR}</textarea></td>
	</tr>
	</table>
	</fieldset>
	<fieldset id="zoneproposition">
	<legend>Propositions</legend>
	<ul id="alignrep">
		<li><input type="radio" name="Disp" id="idhor" value="Hor" {d1} /><label for="idhor">Horizontale</label></li>
		<li><input type="radio" name="Disp" id="idver" value="Ver" {d2} /><label for="idver">Verticale</label></li>
	</ul>
	{RetourReponseQRModif} 
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_RADIO-]
[BLOCK_MODIF_COCHER+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Enoncé</legend>
	<ul>
		<li><input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label></li>
		<li><input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label></li>
	</ul>
	<label for="idenonce">{sMessageErreur1} Enoncé :</label>
	<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQC}</textarea>
	</fieldset>
	<fieldset id="zonereponse">
	<legend>Zone réponse</legend>
	<ul id="alignrep">
		<li><input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label></li>
		<li><input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label></li>
		<li><input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label></li>
		<li><input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label></li>
	</ul>
	<table border="0" cellpadding="1" cellspacing="10">
	<tr>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAv">{TxtAvQC}</textarea></td>
		<td id="bloczonerep">Zone<br />réponse</td>
		<td width="45%"><textarea cols="40" rows="2" name="TxtAp">{TxtApQC}</textarea></td>
	</tr>
	</table>
	</fieldset>
	<fieldset id="zoneproposition">
	<legend>Propositions</legend>
	<ul id="alignrep">
		<li><input type="radio" name="Disp" id="idhor" value="Hor" {d1} /><label for="idhor">Horizontale</label></li>
		<li><input type="radio" name="Disp" id="idver" value="Ver" {d2} /><label for="idver">Verticale</label></li>
	</ul>
	{RetourReponseQCModif}
	<hr class="sepproprep" />
	<ul>
		<li><label for="idnbrepmax">{sMessageErreur2} Nombre de réponses max :</label><input type="text" size="2" maxlength="2" name="NbRepMax" id="idnbrepmax" value="{NbRepMaxQC}" onblur="verifNumeric(this)" /></li>
		<li><label for="idmessmax">Message "Maximum dépassé"</label><input type="text" size="70" maxlength="254" name="MessMax" id="idmessmax" value="{MessMaxQC}" /></li>
	</ul>
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_COCHER-]
[BLOCK_MODIF_MPTEXTE+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zoneenonce">
	<legend>Mise en page de type "texte"</legend>
	<ul>
		<li><input type="radio" name="Align" id="idAleft" value="left" {ae1} /><label for="idAleft">Gauche</label></li>
		<li><input type="radio" name="Align" id="idAright" value="right" {ae2} /><label for="idAright">Droite</label></li>
		<li><input type="radio" name="Align" id="idAcenter" value="center" {ae3} /><label for="idAcenter">Centrer</label></li>
		<li><input type="radio" name="Align" id="idAjustify" value="justify" {ae4} /><label for="idAjustify">Justifier</label></li>
	</ul>
	<label for="idtexte">{sMessageErreur1} Texte :</label>
	<textarea name="Texte" id="idtexte" rows="8" cols="70">{TexteMPT}</textarea>
	</fieldset>
<input type="hidden" name="envoyer" value="1" />
</form>
{sRecharger}
[BLOCK_MODIF_MPTEXTE-]
[BLOCK_MODIF_MPSEP+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
	<label for="idordreobj">Numéro d'ordre : </label>
	<select name="ordreobj" id="idordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<fieldset id="zonempsep">
	<legend>Mise en page de type "séparateur"</legend>
	<ul>
		<li><input type="radio" name="Align" id="idAleft" value="left" {ae1} /><label for="idAleft">Gauche</label></li>
		<li><input type="radio" name="Align" id="idAright" value="right" {ae2} /><label for="idAright">Droite</label></li>
		<li><input type="radio" name="Align" id="idAcenter" value="center" {ae3} /><label for="idAcenter">Centrer</label></li>
		<li><input type="radio" name="Align" id="idAjustify" value="justify" {ae4} /><label for="idAjustify">Justifier</label></li>
	</ul>
	<label for="idlargeur">{sMessageErreur1} Largeur :</label>
	<input type="text" size="4" maxlength="4" name="Largeur" id="idlargeur" value="{LargeurMPS}" onblur="verifNumeric(this)" />
	<input type="radio" name="TypeLarg" id="idpour" value="P" {sAR1} /><label for="idpour">pourcents</label>
	<input type="radio" name="TypeLarg" id="idpix" value="N" {sAR2} /><label for="idpix">pixels</label>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
</form>
{sRecharger}
[BLOCK_MODIF_MPSEP-]
[BLOCK_MODIF_FORMUL+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
	<fieldset>
	<legend>Titre de l'activité en ligne</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur1} <label for="idtitre">Titre :</label>
		</td>
		<td>
			<input id="idtitre" type="text" size="70" maxlength="100" name="Titre" value="{Titre}" />
		</td>
	</tr>
	<tr>
		<td>
			Encadrer :
		</td>
		<td>
			<input id="idencoui" type="radio" name="Encadrer" value="1" {sEncadr1} /><label for="idencoui">Oui</label>
			<input id="idencnon" type="radio" name="Encadrer" value="0" {sEncadr2} /><label for="idencnon">Non</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>Mise en page</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur2} <label for="idlarg">Largeur des marges :</label>
		</td>
		<td>
			<input id="idlarg" type="text" size="3" maxlength="3" name="Largeur" value="{Largeur}" />
		</td>
		<td>
			<input id="idpourc" type="radio" name="TypeLarg" value="P" {sTypeLargeur1} /><label for="idpourc">pourcents</label>
			<input id="idpix" type="radio" name="TypeLarg" value="N" {sTypeLargeur2} /><label for="idpix">pixels</label>
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur3} <label for="idintel">Interligne éléments :</label>
		</td>
		<td>
			<input id="idintel" type="text" size="3" maxlength="3" name="InterElem" value="{InterElem}" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur4} <label for="idinter">Interligne énoncé-réponse :</label>
		</td>
		<td>
			<input id="idinter" type="text" size="3" maxlength="3" name="InterEnonRep" value="{InterEnonRep}" />
		</td>
	</tr>
	</table>
	</fieldset>			
	<fieldset>
	<legend>Options supplémentaires</legend>
	<ul id="options">
		<li>
			Type : <input id="idpriv" type="radio" name="Type" value="prive" {sType1} /><label for="idpriv">Privé</label> 
			<input id="idpub" type="radio" name="Type" value="public" {sType2} /><label for="idpub">Public</label>
		</li>
		<li>
			<label for="idremptout">Tous les champs doivent être remplis :</label>
			<input id="idremptout" type="checkbox" name="RemplirTout" value="1" {sRemplirToutSel} />
		</li>
		<li>
			<label for="idautocorrect">Auto correction :</label>
			<input id="idautocorrect" type="checkbox" name="AutoCorrection" value="1" {sAutoCorrectionSel} />
		</li>
		<li>
			Méthode de calcul du score :
			<dl id="calcul">
				<dt><input type="radio" name="Methode" id="Meth_0" value="0" {sMethode_0} /><label for="Meth_0">Formule standard</label></dt>
				<dd>( Nombre de réponses correctes fournies / Nombre de réponses correctes attendues)</dd>
				<dt><input type="radio" name="Methode" id="Meth_1" value="1" {sMethode_1} /><label for="Meth_1">Formule avancée</label></dt>
				<dd>[ ( Nombre de réponses correctes fournies / Nombre de réponses correctes attendues ) - ( Nombre de réponses incorrectes incorrectes / Nombre total de réponses incorrectes ) ]</dd>
			</dl>
		</li>
	</ul>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />   
</form>
{sRecharger}
[BLOCK_MODIF_FORMUL-]
[BLOCK_INTRO+]
<div id="titrepagevierge">
	<img src="../../images/doc-vide.gif" alt="logo" />
	Générateur d'activités en ligne
	<span id="ute">Unit&eacute; de Technologie de l'&Eacute;ducation</span>
</div>
[BLOCK_INTRO-]
</div>
</body>
</html>
