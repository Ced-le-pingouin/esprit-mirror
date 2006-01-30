<?php require_once ("globals.inc.php"); ?>

<html>

<head>
<?php link_theme (); echo LienVersFichierCSS ("bottom.css").LienVersFichierCSS ("dialogue.css"); ?>
</head>

<body>
<table border="0" cellspacing="1" cellpadding="2" width="100%" height="100%">
<tr>
<td class=""><a href="javascript: top.close();">Changer son mot de passe</a></td>
<td class="dialogue_menu">
<a href="javascript: top.frames['principal'].document.forms[0].submit(); void(0);" onclick="blur()">Enregistrer</a>
&nbsp;<span class="dialogue_separateur_menu">|</span>&nbsp;
<a href="javascript: top.close();">Fermer</a></td>
</tr>
</table>
</body>

</html>
