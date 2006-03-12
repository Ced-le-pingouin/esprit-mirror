<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
[BLOCK_STYLESHEET_ERREUR+]<link type="text/css" rel="stylesheet" href="theme://dialogue/dialog-important.css">[BLOCK_STYLESHEET_ERREUR-]
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
<script type="text/javascript" language="javascript">
<!--
function init()
{
	top.oMenu().location = "modifier-menu.php"
		+ "?modaliteFenetre={fenetre->modalite}"
		+ "&menu=sujet";
}
function envoyer()
{
	var bOk = false;
	if (document.forms[0].elements["modaliteFenetre"].value == "supprimer" ||
		document.forms[0].elements["titreSujet"].value.length > 0)
		bOk = true;
	
	if (bOk)
	{
		if (document.getElementById)
		{
			top.oMenu().page_desactiver();
			document.getElementById('id_barre_de_progression').style.visibility = "visible";
		}
		document.forms[0].submit();
	}
	else
	{
		alert("Le nouveau sujet ne contient pas de titre");
	}
}
function insererBalise(v_sBaliseDepart,v_sBaliseFin) { insertAtCursor(document.forms[0].elements["{editeur->nom}"],v_sBaliseDepart,v_sBaliseFin); }
//-->
</script>
<style type="text/css">
<!--
.largeur_page { width: 100%; }
td.intitule { text-align: right; vertical-align: middle; }
div.barre_de_progression { background-color: rgb(255,255,255); position: absolute; top: 0; left: 5; width: 100%; height: 100%; visibility: hidden; }
textarea.editeur_texte { width: 100%; height: 320px; }
-->
</style>
</head>
<body onload="init()">
<form action="modifier_sujet.php" enctype="multipart/form-data" method="post">
[BLOCK_SUJET+][BLOCK_SUJET-]
<input type="hidden" name="modaliteFenetre" value="{fenetre->modalite}">
<input type="hidden" name="idForum" value="{forum->id}">
<input type="hidden" name="idSujet" value="{sujet->id}">
<input type="hidden" name="idNiveau" value="{niveau->id}">
<input type="hidden" name="typeNiveau" value="{niveau->type}">
<input type="hidden" name="idEquipe" value="{equipe->id}">
</form>
{barre_de_progression}
</body>
</html>

[SET_MODIFIER_SUJET+]
{onglet->sujet}
<br>
{onglet->message}
[SET_MODIFIER_SUJET-]

[SET_TITRE_SUJET+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr>
<td class="intitule">Titre&nbsp;:&nbsp;</td>
<td width="99%"><input type="text" name="titreSujet" size="50" value="{titre->valeur}" class="largeur_page"></td>
</tr>
[BLOCK_TITRE_SUJET_MESSAGE+]
<tr>
<td>&nbsp;</td>
<td width="99%" style="font-size: 7pt;">Cette fenêtre vous permet de créer un nouveau sujet et non un nouveau message. Fermez cette fenêtre et cliquez sur le lien &laquo;&nbsp;Nouveau message&nbsp;&raquo; après avoir sélectionné le sujet ad hoc si votre intention est de déposer un message et non d'ajouter un nouveau sujet. La création d'un nouveau sujet ne devrait se faire que lorsqu'aucun sujet actuellement disponible ne convient. Faites-le donc avec retenue. Trop de sujets dans un forum rend le forum confus et difficile à utiliser.</td>
</tr>
[BLOCK_TITRE_SUJET_MESSAGE-]
</table>
[SET_TITRE_SUJET-]

[SET_MESSAGE_SUPPRIMER_SUJET+]Vous &ecirc;tes sur le point de supprimer un sujet.[SET_MESSAGE_SUPPRIMER_SUJET-]
[SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES+]Attention, le sujet que vous d&eacute;sirez supprimer est utilis&eacute; dans le forum de toutes les &eacute;quipes. Sa suppression entra&icirc;nera sa suppression dans tous les forums.[SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES-]
[SET_QUESTION_SUPPRIMER_SUJET+]Voulez-vous continuer&nbsp;?[SET_QUESTION_SUPPRIMER_SUJET-]

[SET_BARRE_DE_PROGRESSION_MESSAGE+]Un instant svp[SET_BARRE_DE_PROGRESSION_MESSAGE-]

