<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript">
<!--
function init()
{
	var oFrmMenu = top.document.getElementsByName('Bas').item(0);
	oFrmMenu.setAttribute("src","{frames['menu']->url}");
}
[BLOCK_JAVASCRIPT_FUNCTION_VALIDER+]
[VAR_COPIE_COURRIEL_VALIDER+]
document.forms[0].submit();###
top.oFrmListeEquipes().valider();
[VAR_COPIE_COURRIEL_VALIDER-]
function valider()
{
	{valider}
}
[BLOCK_JAVASCRIPT_FUNCTION_VALIDER-]
//-->
</script>
</head>
<body onload="init()">
{html_form}
[BLOCK_COPIE_COURRIEL+][BLOCK_COPIE_COURRIEL-]
<input type="hidden" name="idForum" value="{forum->id}">
{/html_form}
</body>
</html>

[SET_SANS_EMAIL+]
<table border="0" cellspacing="5" cellpadding="0" width="100%">
<tr><td>&nbsp;</td><td><img src="commun://espacer.gif" width="1" height="20" border="0" alt=""></td></tr>
<tr>
<td rowspan="3" valign="top"><img src="commun://icones/64x64/important.gif" width="64" height="64" border="0"></td>
<td><p>La copie courriel vous permet d'obtenir une copie des nouveaux messages qui sont d&eacute;pos&eacute;s dans ce forum. Cette copie vous est transmise par courriel (mail).</p></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td><p>Mais dans votre cas, nous remarquons qu'il n'y a pas d'adresse courriel associ&eacute;e à votre profil. Cliquez sur le lien &laquo;&nbsp;Profil&nbsp;&raquo; si vous d&eacute;sirez introduire une adresse et b&eacute;n&eacute;ficier de cette fonctionnalit&eacute;.</p></td></tr>
</table>
[SET_SANS_EMAIL-]


[SET_EMAIL_ERRONE+]
<table border="0" cellspacing="5" cellpadding="0" width="100%">
<tr><td>&nbsp;</td><td><img src="commun://espacer.gif" width="1" height="20" border="0" alt=""></td></tr>
<tr>
<td rowspan="3" valign="top"><img src="commun://icones/64x64/important.gif" width="64" height="64" border="0"></td>
<td><p>La copie courriel vous permet d'obtenir une copie des nouveaux messages qui sont d&eacute;pos&eacute;s dans ce forum. Cette copie vous est transmise par courriel (mail).</p></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td><p>Mais dans votre cas, nous remarquons que l'adresse qui se trouve dans votre profil n'est pas une adresse &laquo;&nbsp;valable&raquo;&nbsp; (elle ne correspond pas à un format correct). Cliquez sur le lien &laquo;&nbsp;Profil&nbsp;&raquo; pour corriger votre adresse et b&eacute;n&eacute;ficier de cette fonctionnalit&eacute;.</p></td></tr>
</table>
[SET_EMAIL_ERRONE-]

[SET_MESSAGE_COMMUN+]
<p>La copie courriel vous permet d'obtenir une copie des nouveaux messages qui sont d&eacute;pos&eacute;s dans un forum. Cette copie vous est transmise par courriel (mail) à l'adresse &laquo;&nbsp;{personne->email}&nbsp;&raquo;. Cliquez sur le lien Profil si vous d&eacute;sirez modifier cette adresse.</p>
[SET_MESSAGE_COMMUN-]

[SET_COPIE_COURRIEL+]
<table border="0" cellspacing="3" cellpadding="0" width="100%">
<tr><td colspan="2"><img src="commun://espacer.gif" width="1" height="20" border="0" alt=""></td></tr>
<tr><td valign="top"><input type="checkbox" id="id_copie_courriel" name="copieCourriel" onfocus="blur()"{copieCourriel->selectionne}></td><td colspan="2" width="99%"><label for="id_copie_courriel">Je veux que l'on m'envoie une copie des nouveaux messages d&eacute;pos&eacute; dans ce forum à l'adresse indiqu&eacute;e dans mon profil.</label></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>{message_commun}</td></tr>
</table>
[SET_COPIE_COURRIEL-]

[SET_COPIE_COURRIEL_EQUIPES+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td><img src="commun://espacer.gif" width="1" height="10" border="0" alt=""></td></tr>
<tr><td>Je veux que l'on m'envoie une copie des nouveaux messages d&eacute;pos&eacute;s dans les forums des &eacute;quipes suivantes&nbsp;:</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><iframe src="{iframe->src}" name="LISTE_EQUIPES" width="100%" height="200" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto"></iframe></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>{message_commun}</td></tr>
</table>
[SET_COPIE_COURRIEL_EQUIPES-]

