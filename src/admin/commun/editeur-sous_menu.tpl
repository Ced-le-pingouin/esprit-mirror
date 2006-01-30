<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://barre_outils.css">
<script type="text/javascript" language="javascript">
<!--
function afficher_editer() { document.getElementById("idVisualiser").className = "cacher"; document.getElementById("idEditer").className = "afficher"; }
function afficher_visualiser() { document.getElementById("idEditer").className = "cacher"; document.getElementById("idVisualiser").className = "afficher"; }
//-->
</script>
<style type="text/css">
<!--
body.toolbar { border: rgb(0,0,0) none 1px; border-top-style: solid; }
#idEditer, #idVisualiser { position: absolute; left: 0px; top; 0px; width: 100%; height: 100%; }
.afficher { visibility: visible; display: block; }
.cacher { visibility: hidden; display: none; }
-->
</style>
</head>
<body class="toolbar">
<div id="idVisualiser" class="afficher">
<table border="0" cellspacing="0" cellpadding="3" width="100%" height="100%">
<tr>
<td>&nbsp;</td>
<td style="text-align: right; width: 99%"><a href="javascript: void(0);" onclick="return top.oPrincipale().visualiser()" onfocus="blur()">Visualiser le r&eacute;sultat</a></td>
<td>&nbsp;</td>
</tr>
</table>
</div>
<div id="idEditer" class="cacher">
<table border="0" cellspacing="0" cellpadding="3" width="100%" height="100%">
<tr>
<td>&nbsp;</td>
<td style="text-align: right; width: 99%"><a href="javascript: void(0);" onclick="return top.oPrincipale().editeur()" onfocus="blur()">Retourner &agrave; l'&eacute;diteur</a></td>
<td>&nbsp;</td>
</tr>
</table>
</div>
</body>
</html>

