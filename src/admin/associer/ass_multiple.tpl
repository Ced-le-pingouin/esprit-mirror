<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="language" src="javascript://globals.js"></script>
<script type="text/javascript" language="language" src="ass_multiple.js"></script>
<script type="text/javascript" language="language">
<!--
var g_sRech = null;
//-->
</script>
<style type="text/css">
<!--
body { background-image: none; }
td.intitule { vertical-align: middle; text-align: right; }
iframe.abc { border-style: none; }
-->
</style>
</head>
<body>
<form>
<table border="0" cellspacing="0" cellpadding="2" width="100%" align="top">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
<td valign="top"><img src="theme://onglet/onglet_tab-1x1.gif" border="0"></td>
<td class="onglet_tab_1x2">&nbsp;{titre->onglet->personnes}&nbsp;</td>
<td valign="top"><img src="theme://onglet/onglet_tab-1x3.gif" border="0"></td>
<td class="intitule" width="99%">Rechercher&nbsp;:&nbsp;</td>
<td><input type="text" name="rechercher" size="30" onkeyup="rechPersonne(value,oFramePersonnes())" value=""></td>
<td><img src="commun://espacer.gif" width="2" height="23" border="0"></td>
<td><input type="button" onclick="this.form.elements['rechercher'].value=''; this.form.elements['rechercher'].focus()" value="Effacer"></td>
</tr>
</table>
<iframe name="personnes" src="ass_multiple-pers.php?ID_FORM={formation->id}&STATUT_PERS={statut->id}" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="150" scrolling="yes"></iframe>
</td>
</tr>
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
<td><img src="theme://onglet/onglet_tab-1x1.gif" border="0"></td>
<td class="onglet_tab_1x2">&nbsp;Liste&nbsp;des&nbsp;cours&nbsp;</td>
<td><img src="theme://onglet/onglet_tab-1x3.gif" border="0"></td>
<td><img src="commun://espacer.gif" width="2" height="23" border="0"></td>
<td width="99%" align="right"><img src="theme://icones/ajouter-bas.gif" border="0">&nbsp;<a href="javascript: ajouterPersonnes(); void(0);">ajouter</a>&nbsp;|&nbsp;<img src="theme://icones/retirer-haut.gif" border="0">&nbsp;<a href="javascript: retirerPersonnes(); void(0);">Retirer</td>
<td>&nbsp;</td>
</tr>
</table>
<iframe name="inscrits" src="ass_multiple-inscrits.php" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="240" scrolling="yes"></iframe>
</td>
</tr>
</table>
<!--<iframe src="ass_multiple-abc.php" class="abc" frameborder="0" marginwidth="0" marginheight="0" width="50" height="440"></iframe>-->
</form>
</body>
</html>
