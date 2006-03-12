<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="commun://js/login.js.php"></script>
<style type="text/css">
<!--
body { background: rgb(175,165,138) url('theme://login/login-gauche-fond.jpg') repeat-y fixed top left; }
td { color: rgb(0,0,0); font-family: Verdana,Tahoma,Arial; font-size: 8pt; }
input { background-color: rgb(255,255,255); color: rgb(0,0,0);  border: rgb(0,0,0) solid 1px; font-family: Verdana,Tahoma,Arial; font-size: 8pt; }
.btn_ok { background-color: rgb(148,3,32); color: rgb(255,255,255);  border: rgb(0,0,0) solid 1px; }
a { color: rgb(255,255,255); font-family: Verdana,Tahoma,Arial; font-size: 8pt; font-weight: normal; }
td.erreur_login { background-color: rgb(253,249,238); border: rgb(136,37,37) dashed 1px; color: rgb(136,37,37); text-align: center; padding: 5px; }
td.avertissement_login { background-color: rgb(253,249,238); border: rgb(136,37,37) dashed 1px; color: rgb(136,37,37); padding: 5px; }
-->
</style>
</head>
<body>
{form}
<img src="theme://login/login-gauche-logo.jpg" width="228" height="304" border="0">
<table border="0" cellspacing="5" cellpadding="0" width="200" align="right">
[BLOCK_ERREUR_LOGIN+]
<tr><td colspan="2" class="erreur_login">Votre pseudo ou votre mot de passe est incorrect.</td></tr>
[BLOCK_ERREUR_LOGIN-]
<tr><td colspan="2">Si vous êtes inscrit, introduisez votre pseudo et mot de passe.</td></tr>
<!--<tr><td colspan="2">&nbsp;</td></tr>-->
<tr><td colspan="2" style="text-align: right;"><a href="javascript: void(0);" onclick="return mdp_oublier()" onfocus="blur()" style="font-size: 7pt;">Oubli&eacute;&nbsp;?</a></td></tr>
<tr><td align="right" valign="middle" width="99%">Pseudo&nbsp;:</td><td align="right" valign="middle"><input type="text" size="13" name="idPseudo"></td></tr>
<tr><td align="right" valign="middle" width="99%">Mot&nbsp;de&nbsp;passe&nbsp;:</td><td align="right" valign="middle"><input type="password" size="13" name="idMdp"></td></tr>
<tr><td align="right" colspan="2"><input class="btn_ok" type="submit" value="&nbsp;Ok&nbsp;"></td></tr>
[BLOCK_AVERTISSEMENT_LOGIN+]<tr><td colspan="2" class="avertissement_login">{login.avertissement}</td></tr>[BLOCK_AVERTISSEMENT_LOGIN-]
</table>
{/form}
</p>&nbsp;</p>
</body>
</html>
