<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://exporter-personnes.css">
<script type="text/javascript" language="javascript" src="exporter.js"></script>
<script type="text/javascript" language="javascript">
<!--
function rechercher() { document.forms[0].submit(); }
//-->
</script>
</head>
<body onload="rechercher()">
<form name="form_filtres" action="exporter-personnes.php" target="personnes" method="get">
<table border="0" cellspacing="2" cellpading="2" width="100%">
<tr>
<td><span class="intitule">&nbsp;Sessions&nbsp;:&nbsp;</span></td>
<td>
<select name="ID_FORM" onchange="rechercher()">
<!--<option value="0">Toutes</option>-->
[BLOCK_OPTION_FORMATION+]
<option value="{formation->id}">{formation->nom}</option>
[BLOCK_OPTION_FORMATION-]
</select>
</td>
<td><span class="intitule">&nbsp;statuts&nbsp;:&nbsp;</span></td>
<td>
<select name="ID_STATUT" onchange="rechercher()">
<option value="0">Tous</option>
[BLOCK_STATUT_PERSONNE+]<option value="{statut.id}">{statut.nom}</option>[BLOCK_STATUT_PERSONNE-]
</select>
</td>
<td width="99%">&nbsp;<a href="javascript: void(0);" onclick="rechercher()" onfocus="blur()">Rafra&icirc;chir</a></td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>&nbsp;</td>
<td><img src="theme://espacer.gif" width="5" height="20" border="0"></td>
<td><img src="theme://onglet/onglet_tab-1x1.gif" border="0"></td>
<td class="onglet_tab_1x2" nowrap="nowrap">&nbsp;{onglet->titre}&nbsp;</td>
<td><img src="theme://onglet/onglet_tab-1x3.gif" border="0"></td>
<td width="99%" align="right">{liste_alphabet}</td>
<td>&nbsp;</td>
</tr>
</table
</form>
</body>
</html>
