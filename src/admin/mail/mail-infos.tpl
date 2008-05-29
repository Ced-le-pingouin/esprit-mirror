<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://mail.css">
<link type="text/css" rel="stylesheet" href="css://commun/barre_outils.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_sTitreFenetre = null;

function changerTitreFenetre()
{
	if (top.document.title)
	{
		if (g_sTitreFenetre == null)
			g_sTitreFenetre = top.document.title;
		
		top.document.title = g_sTitreFenetre + ": "
			+ (document.forms[0].elements["sujetCourriel"].value.length > 0 ? document.forms[0].elements["sujetCourriel"].value : "(Pas de sujet)");
	}
}

function envoyer()
{
	// Retirer les espaces supplémentaires
	var sSujetCourriel = document.forms[0].elements["sujetCourriel"].value.trim();
	document.forms[0].elements["sujetCourriel"].value = sSujetCourriel;
	
	var sMessageCourriel = top.oPrincipale().document.forms[0].elements["messageCourriel"].value.trim();
	top.oPrincipale().document.forms[0].elements["messageCourriel"].value = sMessageCourriel;
	
	if (sMessageCourriel.length < 1)
	{
		alert("Vous avez oublié d'introduire votre message");
		top.oPrincipale().document.forms[0].elements["messageCourriel"].focus();
		return;
	}
	
	var i;
	var oEmails = self.frames["emails"].document.forms[0].elements["destinataireCourriel[]"];
	var iCompteurEmailsSelect = 0;
	
	if (typeof(oEmails.length) == "undefined")
		oEmails = new Array(self.frames["emails"].document.forms[0].elements["destinataireCourriel[]"]);
	
	// Vérifier qu'au moins une personne a été sélectionné
	for (i=0; i<oEmails.length; i++)
		if (oEmails[i].checked) iCompteurEmailsSelect++;
	
	// Envoyer le courriel
	if (iCompteurEmailsSelect > 0)
	{
		// Placer les nouveaux éléments dans le div caché
		var oDiv = document.getElementById("id_emails");
		
		// Récupérer le message
		document.forms[0].elements["messageCourriel"].value = sMessageCourriel
			+ "\r\n\r\n"
			+ document.forms[0].elements["messageCourriel"].value;
		
		// Récupérer la liste des destinataires
		var oInput;
		
		for (i=0; i<oEmails.length; i++)
		{
			oInput = document.createElement("input");
			oInput.setAttribute("name","destinataireCourriel[]");
			oInput.setAttribute("value",(oEmails[i].checked ? "" : "*") + oEmails[i].value);
			oDiv.appendChild(oInput);
		}
		
		document.forms[0].submit();
	}
	else
	{
		alert("Pour envoyer un courriel, il faut au moins sélectionner une personne");
	}
}
//-->
</script>
</head>
<body onload="changerTitreFenetre()">
{form}
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr><td>&nbsp;</td><td><div class="intitule" style="text-align: right;">De&nbsp;:&nbsp;&nbsp;</div></td><td width="99%"><select name="expediteurCourriel">{html_options}</select></td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td valign="top"><div class="intitule" style="text-align: right; padding-top: 5px;">A&nbsp;:&nbsp;&nbsp;</div></td><td width="99%"><iframe src="{iframe->src}" name="emails" width="100%" height="80" frameborder="0" marginwidth="0" marginheight="0" scrolling="yes"></iframe></td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td><div class="intitule" style="text-align: right;">Sujet&nbsp;:&nbsp;&nbsp;</div></td><td width="99%"><input type="text" name="sujetCourriel" value="{sujet_courriel}" onkeyup="changerTitreFenetre()"></td><td>&nbsp;</td></tr>
</table>
<div id="id_emails" style="visibility: hidden; display: none;">
<textarea name="messageCourriel">{message_courriel}</textarea>
</div>
{/form}
</body>
</html>
