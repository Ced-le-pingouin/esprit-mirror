<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
[BLOCK_STYLESHEET_ERREUR+]<link type="text/css" rel="stylesheet" href="css://sousactive/forum.css">[BLOCK_STYLESHEET_ERREUR-]
<script type="text/javascript" language="javascript" src="javascript://tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" language="javascript" src="editeur://editeur_forum.js"></script>
<script type="text/javascript">
initEditeur("textareas", "", {tableau_de_bord->actif} );
</script>
<script type="text/javascript" language="javascript">
<!--
function mySetContent(editor_id, body, doc) {
	if (top.recuperer) {
		body.innerHTML = top.recuperer();
	}
	return "";
}
var g_oEditeur = null;
function init()
{
	top.oMenu().location = "modifier-menu.php"
		+ "?modaliteFenetre={fenetre->modalite}"
		+ "&menu=message";
	
	if (document.forms[0].elements["modaliteFenetre"].value.indexOf("supprimer") == -1)
	{
		g_oEditeur = document.forms[0].elements["{editeur->nom}"];
		g_oEditeur.value = document.forms[0].elements["editeur_sauvegarde"].value;
	}
}
function valider()
{
	tinyMCE.triggerSave();
	var oForm = document.forms[0];
	if (oForm.elements["{editeur->nom}"].value.length < 1) { alert("Votre message est vide : "+oForm.elements["{editeur->nom}"].value); return false; }
	if (document.getElementById)
	{
		top.oMenu().page_desactiver();
		document.getElementById("id_barre_de_progression").style.visibility = "visible";
	}
	return oForm.submit();
}
function Annuler()
{
	// Desactiver le message sélectionné
	if (top.opener && top.opener.top.oFrmMessages)
		top.opener.top.oFrmMessages().deselect_message();
	top.close();
}
function supprimer() { return document.forms[0].submit(); }
//-->
</script>

</head>
<body onload="init()" class="dialog_important">
<form action="{form->action}" method="post" enctype="multipart/form-data">
[BLOCK_MESSAGE+][BLOCK_MESSAGE-]
<input type="hidden" name="modaliteFenetre" value="{fenetre->modalite}">
<input type="hidden" name="idSujet" value="{sujet->id}">
<input type="hidden" name="idMessage" value="{message->id}">
<input type="hidden" name="idNiveau" value="{niveau->id}">
<input type="hidden" name="typeNiveau" value="{niveau->type}">
<input type="hidden" name="idEquipe" value="{equipe->id}">
<div style="visibility: hidden; display: none;"><textarea name="editeur_sauvegarde">{editeur->sauvegarde}</textarea></div>
</form>
{barre_de_progression}
</body>
</html>

[SET_MESSAGE_SUPPRIMER_SUJET+]Vous &ecirc;tes sur le point de supprimer un message.[SET_MESSAGE_SUPPRIMER_SUJET-]
[SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES+]Attention, le message que vous d&eacute;sirez supprimer est utilis&eacute; dans le forum de toutes les &eacute;quipes. Sa suppression entra&icirc;nera sa suppression dans tous les forums.[SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES-]
[SET_QUESTION_SUPPRIMER_SUJET+]Voulez-vous continuer&nbsp;?[SET_QUESTION_SUPPRIMER_SUJET-]

[SET_FICHIER_ATTACHE+]
<fieldset>
<legend>&nbsp;Fichier attach&eacute;&nbsp;</legend>
<input type="file" name="fichierMessage" size="85">
[BLOCK_EFFACER_FICHIER_ATTACHE+]
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;&nbsp;</td>
<td><input type="checkbox" name="effacerFichierMessage" onfocus="blur()"></td>
<td>&nbsp;&nbsp;</td>
<td><small>Effacer le fichier attach&eacute; &laquo;&nbsp;{fichier_attache->nom}&nbsp;&raquo;</small></td>
</tr>
</table>
[BLOCK_EFFACER_FICHIER_ATTACHE-]
</fieldset>
[SET_FICHIER_ATTACHE-]

[SET_BARRE_DE_PROGRESSION_MESSAGE+]Un instant svp[SET_BARRE_DE_PROGRESSION_MESSAGE-]

