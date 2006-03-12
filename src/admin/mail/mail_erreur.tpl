<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<script type="text/javascript" language="javascript">
<!--
function init()
{
	if (document.forms.length > 0)
	{
		var oSelect = document.forms[0].elements["listeDestinatairesErrones"];
		
		if (oSelect)
			for (var i=0; i<top.asListeDestinatairesErrones.length; i++)
				oSelect.options[i] = new Option(unescape(top.asListeDestinatairesErrones[i]));
	}
}
//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
<td valign="top"><img src="commun://icones/64x64/courriel_envoye.gif" width="64" height="64" alt="" border="0"></td>
<td><img src="commun://espacer.gif" width="10" height="1" alt="" border="0"></td>
<td width="99%" valign="top"><p style="font-size: 10pt; font-weight: bold;">Envoi courriel</p>{envoi_courriel->message}</td>
<td>&nbsp;</td>
</tr>
</table>
</body>
</html>

[SET_ENVOI_COURRIEL_REUSSI+]
<p>Le courriel a bien été envoyé au membre de cette liste.</p>
[SET_ENVOI_COURRIEL_REUSSI-]

[SET_ENVOI_COURRIEL_ECHOUE+]
[VAR_ERREUR_PARTIELLE+]
<p>Votre courriel a bien été envoyé aux personnes suivantes&nbsp;:</p>
<form>
<select name="listeDestinatairesErrones" size="10" style="width: 260px;">
</select>
</form>
[VAR_ERREUR_PARTIELLE-]

[VAR_ERREUR_COMPLETE+]Tous les membres de la liste n'ont pas reçu le courriel.[VAR_ERREUR_COMPLETE-]
{envoi_courriel->message}
[SET_ENVOI_COURRIEL_ECHOUE-]
