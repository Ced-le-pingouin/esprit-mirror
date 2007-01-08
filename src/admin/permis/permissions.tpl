<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title></title>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<style type="text/css">
<!--
body { background-image: none; }
-->
</style>

<script type="text/javascript" language="javascript" src="js://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="js://globals.js"></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>


<script type="text/javascript">

function showOnly( id ) {       
	if ('{onglet}' != id)
	window.location = "{self}?onglet="+id;
       
}

</script>

</head>

<body>
[BLOCK_LISTE_PERMISSION+]
<form action="permissions.php" method="post" target="Principale">
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr>
<td class="cellule_sous_titre">&nbsp;Permissions&nbsp;</td>
<td class="cellule_sous_titre">&nbsp;Oui&nbsp;</td>
<td class="cellule_sous_titre">&nbsp;Non&nbsp;</td>
</tr>
[BLOCK_PERMISSION+]
<tr>
<td class="{permission.td.class}" width="99%"><span title="{permission.nom}" style="cursor: help;">{permission.description}</span></td>
<td class="{permission.td.class}"><div style="text-align: center"><input type="radio" name="idPermis[{permission.input.name}]" value="1" onfocus="blur()"{permission.input.oui.checked}></div></td>
<td class="{permission.td.class}"><div style="text-align: center"><input type="radio" name="idPermis[{permission.input.name}]" value="0" onfocus="blur()"{permission.input.non.checked}></div></td>
</tr>

[BLOCK_PERMISSION-]
</table>
<input type="hidden" name="onglet" value="permission" />
<input type="hidden" name="idStatut" value="{idStatut}">
<input type="hidden" name="filtre" value="{filtre}">
</form>
[BLOCK_LISTE_PERMISSION-]


[BLOCK_ADMIN+]
<form action="{self}" method="post" target="Principale">

<table border="0" cellpadding="4" cellspacing="0" width="100%">
<tr>
<td width="50%" height="1%">
<span class="intitule">Filtre&nbsp;:</span>
<select name="FILTRE" style="width: 100%" onchange="changerFiltre(value,STATUT_PERS.value,1,retIdModule(),FORMATION.checked)">
<option value="-1" selected>Tous</options>
</select>
</td>
<td>&nbsp;</td>
<td class="intitule" height="1%">Statut&nbsp;des&nbsp;personnes&nbsp;:<br>
<select name="STATUT_PERS" style="width: 100%" onchange="changerStatut(value,1)">
<option value="2">Administrateur</options>
</select></td>
</tr>
<tr>
<td rowspan="2" class="intitule" valign="top" align="right">
<iframe name="FRM_PERSONNE" src="liste_personnes.php" width="99%" height="300" frameborder="0"></iframe><br>
Rechercher&nbsp;:&nbsp;<input type="text" name="nomPersonneRech" onkeyup="rechPersonne(value,self.frames['FRM_PERSONNE'],'nom[]')" value="" size="30">
</td>
<td align="center" width="1%"><span title="Ajouter une/des personne(s) &agrave; la liste des personnes inscrites"><input type="button" value="&nbsp;&raquo;&nbsp;" onclick="envoyerPersonnes()"></span><br><br><span title="Enlever une personne de la liste des personnes inscrites"><input type="button" value="&nbsp;&laquo;&nbsp;" onclick="enleverPersonneInscrit()"></span></td>
<td valign="top">
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<tr><td><span class="intitule">&#8250;&nbsp;Liste des personnes ayant le statut</span></td></tr>
<tr><td><iframe name="FRM_INSCRIT" src="liste_roles.php" width="100%" height="180" frameborder="0"></iframe></td></tr>

</table>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td class="intitule" valign="top">
</td>
</tr>
</table>

<input type="hidden" name="onglet" value="admin" />
<input type="hidden" name="idStatut" value="{idStatut}">
<input type="hidden" name="filtre" value="{filtre}">
</form>
[BLOCK_ADMIN-]

</body>
</html>
