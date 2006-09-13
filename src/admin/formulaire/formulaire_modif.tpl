<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<title>Modification des formulaires </title>
<script src="selectionobj.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
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
<body class="modif">
[BLOCK_MODIF_TXTLONG+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<fieldset>
	<legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur1} <label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQTL}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement énoncé :
		</td>
		<td>
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>REPONSE</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur2} <label for="idlargeur">Largeur de la boîte de texte :</label>
		</td>
		<td>
			<input type="text" size="3" maxlength="10" name="Largeur" id="idlargeur" value="{LargeurQTL}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur3} <label for="idhauteur">Hauteur de la boîte de texte :</label>
		</td>
		<td>
			<input type="text" size="3" maxlength="10" name="Hauteur" id="idhauteur" value="{HauteurQTL}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_TXTLONG-]
[BLOCK_MODIF_TXTCOURT+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<fieldset><legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			<label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQTC}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement énoncé :
		</td>
		<td>
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>REPONSE</legend>
	<table>
	<tr>
		<td>
			<label for="idtxtav">Texte avant la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAv" id="idtxtav" value="{TxtAvQTC}" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idtxtap">Texte après la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAp" id="idtxtap" value="{TxtApQTC}" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur1} <label for="idlargeur">Taille de la boîte de texte :</label>
		</td>
		<td>
			<input type="text" size="3" maxlength="3" name="Largeur" id="idlargeur" value="{LargeurQTC}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur2} <label for="idmaxcar">Nombre de caractères maximum :</label>
		</td>
		<td>
			<input type="text" size="3" maxlength="3" name="MaxCar" id="idmaxcar" value="{MaxCarQTC}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_TXTCOURT-]
[BLOCK_MODIF_NOMBRE+]
	<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
	<fieldset>
	<legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			<label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQN}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement énoncé :
		</td>
		<td>
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>REPONSE</legend>
	<table>
	<tr>
		<td>
			<label for="idtxtav">Texte avant la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAv" id="idtxtav" value="{TxtAvQN}" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idtxtap">Texte après la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAp" id="idtxtap" value="{TxtApQN}" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur1} <label for="idnbmin">Nombre minimum :</label>
		</td>
		<td>
			<input type="text" size="10" maxlength="9" name="NbMin" id="idnbmin" value="{NbMinQN}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur2} <label for="idnbmax">Nombre maximum :</label>
		</td>
		<td>
			<input type="text" size="10" maxlength="10" name="NbMax" id="idnbmax" value="{NbMaxQN}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			{sMessageErreur3} <label for="idmultix">Coefficient multiplicateur :</label>
		</td>
		<td>
			<input type="text" size="5" maxlength="10" name="Multi" id="idmultix" value="{MultiQN}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_NOMBRE-]
[BLOCK_MODIF_LISTEDER+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<fieldset><legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			<label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQLD}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement énoncé :
		</td>
		<td>                                    
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset><legend>REPONSE</legend>
	<table>
	<tr>
		<td>
			<label for="idtxtav">Texte avant la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAv" id="idtxtav" value="{TxtAvQLD}" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idtxtap">Texte après la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAp" id="idtxtap" value="{TxtApQLD}" />
		</td>
	</tr>
	<tr>
		<td>
			Réponse(s) : <a href="javascript: soumettre('ajouter',0);">Ajouter</a>
		</td>
	{RetourReponseQCModif}
	<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_LISTEDER-]
[BLOCK_MODIF_RADIO+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<fieldset><legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			<label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" rows="5" cols="70">{EnonQR}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement énoncé :
		</td>
		<td>
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset><legend>REPONSE</legend>
	<table>
	<tr>
		<td> 
			<label for="idtxtav">Texte avant la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAv" id="idtxtav" value="{TxtAvQR}" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idtxtap">Texte après la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAp" id="idtxtap" value="{TxtApQR}" />
		</td>
	</tr>
	<tr>
		<td> 
			Disposition :
		</td>
		<td>
			<input type="radio" name="Disp" id="idhor" value="Hor" {d1} /><label for="idhor">Horizontale</label>
			<input type="radio" name="Disp" id="idver" value="Ver" {d2} /><label for="idver">Verticale</label>
		</td>
	</tr>
	<tr>
		<td> 
			Réponse(s) : <a href="javascript: soumettre('ajouter',0);">Ajouter</a>
		</td>
		{RetourReponseQRModif} 
		<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_RADIO-]
[BLOCK_MODIF_COCHER+]
	<form name="formmodif" action="formulaire_modif.php{sParam}" method="post" enctype="text/html">
	<fieldset>
	<legend>ENONCE</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur1} <label for="idenonce">Enoncé :</label>
		</td>
		<td>
			<textarea name="Enonce" id="idenonce" rows="5" cols="70">{EnonQC}</textarea>
		</td>
	</tr>
	<tr>
		<td>
		Alignement énoncé :
		</td>
		<td>
			<input type="radio" name="AlignEnon" id="idAEleft" value="left" {ae1} /><label for="idAEleft">Gauche</label>
			<input type="radio" name="AlignEnon" id="idAEright" value="right" {ae2} /><label for="idAEright">Droite</label>
			<input type="radio" name="AlignEnon" id="idAEcenter" value="center" {ae3} /><label for="idAEcenter">Centrer</label>
			<input type="radio" name="AlignEnon" id="idAEjustify" value="justify" {ae4} /><label for="idAEjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>REPONSE</legend>
	<table>
	<tr>
		<td>
			<label for="idtxtav">Texte avant la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAv" id="idtxtav" value="{TxtAvQC}" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idtxtap">Texte après la réponse :</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="TxtAp" id="idtxtap" value="{TxtApQC}" />
		</td>
	</tr>
	<tr>
		<td>
			Disposition :
		</td>
		<td>
			<input type="radio" name="Disp" id="idhor" value="Hor" {d1} /><label for="idhor">Horizontale</label>
			<input type="radio" name="Disp" id="idver" value="Ver" {d2} /><label for="idver">Verticale</label>
		</td>
	</tr>
	<tr>
		<td>
			Réponse(s) : <a href="javascript: soumettre('ajouter',0);">Ajouter</a>
		</td>
	{RetourReponseQCModif}
	<tr>
		<td>
			{sMessageErreur2} <label for="idnbrepmax">Nombre de réponses max :</label>
		</td>
		<td>
			<input type="text" size="2" maxlength="2" name="NbRepMax" id="idnbrepmax" value="{NbRepMaxQC}" onblur="verifNumeric(this)" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="idmessmax">Message "Maximum dépassé"</label>
		</td>
		<td>
			<input type="text" size="70" maxlength="254" name="MessMax" id="idmessmax" value="{MessMaxQC}" />
		</td>
	</tr>
	<tr>
		<td>
			Alignement réponse :
		</td>
		<td>
			<input type="radio" name="AlignRep" id="idARleft" value="left" {ar1} /><label for="idARleft">Gauche</label>
			<input type="radio" name="AlignRep" id="idARright" value="right" {ar2} /><label for="idARright">Droite</label>
			<input type="radio" name="AlignRep" id="idARcenter" value="center" {ar3} /><label for="idARcenter">Centrer</label>
			<input type="radio" name="AlignRep" id="idARjustify" value="justify" {ar4} /><label for="idARjustify">Justifier</label>
		</td>
	</tr>
	</table>
	</fieldset>
	<input type="hidden" name="typeaction" value="" />
	<input type="hidden" name="parametre" value="" />
	<input type="hidden" name="envoyer" value="1" />
	</form>
	{sRecharger}
[BLOCK_MODIF_COCHER-]
[BLOCK_MODIF_MPTEXTE+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
<fieldset>
	<legend>Mise en page de type "texte"</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur1}  <label for="idtexte">Texte :</label>
		</td>
		<td>
			<textarea name="Texte" id="idtexte" rows="5" cols="70">{TexteMPT}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			Alignement :
		</td>
		<td>
			<input type="radio" name="Align" id="idAleft" value="left" {ae1} /><label for="idAleft">Gauche</label>
			<input type="radio" name="Align" id="idAright" value="right" {ae2} /><label for="idAright">Droite</label>
			<input type="radio" name="Align" id="idAcenter" value="center" {ae3} /><label for="idAcenter">Centrer</label>
			<input type="radio" name="Align" id="idAjustify" value="justify" {ae4} /><label for="idAjustify">Justifier</label>
		</td>
	</tr>
	</table>
</fieldset>
<input type="hidden" name="envoyer" value="1" />
</form>
{sRecharger}
[BLOCK_MODIF_MPTEXTE-]
[BLOCK_MODIF_MPSEP+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
<fieldset>
	<legend>Mise en page de type "séparateur"</legend>
	<table>
	<tr>
		<td>
			{sMessageErreur1} <label for="idlargeur">Largeur :</label>
		</td>
		<td>
			<input type="text" size="4" maxlength="4" name="Largeur" id="idlargeur" value="{LargeurMPS}" onblur="verifNumeric(this)" />
			<input type="radio" name="TypeLarg" id="idpour" value="P" {sAR1} /><label for="idpour">pourcents</label>
			<input type="radio" name="TypeLarg" id="idpix" value="N" {sAR2} /><label for="idpix">pixels</label>
		</td>
	</tr>
	<tr>
		<td>
			Alignement :
		</td>
		<td>
			<input type="radio" name="Align" id="idAleft" value="left" {ae1} /><label for="idAleft">Gauche</label>
			<input type="radio" name="Align" id="idAright" value="right" {ae2} /><label for="idAright">Droite</label>
			<input type="radio" name="Align" id="idAcenter" value="center" {ae3} /><label for="idAcenter">Centrer</label>
			<input type="radio" name="Align" id="idAjustify" value="justify" {ae4} /><label for="idAjustify">Justifier</label>
		</td>
	</tr>
	</table>
</fieldset>
<input type="hidden" name="envoyer" value="1" />
</form>
{sRecharger}
[BLOCK_MODIF_MPSEP-]
[BLOCK_MODIF_FORMUL+]
<form action="formulaire_modif.php{sParam}" name="formmodif" method="post" enctype="text/html">
<fieldset>
	<legend>Titre du formulaire</legend>
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
	<table>
	<tr>
		<td>
			Type :
		</td>
		<td>
			<input id="idpriv" type="radio" name="Type" value="prive" {sType1} /><label for="idpriv">Privé</label>
		</td>
		<td>
			<input id="idpub" type="radio" name="Type" value="public" {sType2} /><label for="idpub">Public</label>
		</td>
	</tr>
	<tr>
		<td>
			<label for="idremptout">Tous les champs doivent être remplis :</label>
		</td>
		<td colspan="2">
			<input id="idremptout" type="checkbox" name="RemplirTout" value="1" {sRemplirToutSel} />
		</td>
	</tr>
	</table>
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
</body>
</html>
