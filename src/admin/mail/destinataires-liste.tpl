<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="destinataires-liste.css">
</head>
<body>
{form}
<table border="0" cellspacing="1" cellpadding="3" width="100%" style="background-color: rgb(196,202,220);">
[BLOCK_DESTINATAIRE+]
[VAR_DESTINATAIRE+]
<tr><td><input type="checkbox" name="destinataireCourriel[]" value="{destinataire->email:urlencode}" disabled="disabled"></td><td class="courriel_non_conforme"><span style="color: rgb(140,140,140);">{destinataire->email}</span></td></tr>###
<tr><td><input type="checkbox" name="destinataireCourriel[]" value="{destinataire->email:urlencode}" checked="checked"></td><td class="courriel_conforme">{destinataire->email}</td></tr>
[VAR_DESTINATAIRE-]
[BLOCK_DESTINATAIRE-]
</table>
{/form}
</body>
</html>
