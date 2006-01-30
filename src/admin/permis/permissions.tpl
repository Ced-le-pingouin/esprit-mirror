<html>
<head>
<title></title>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<style type="text/css">
<!--
body { background-image: none; }
-->
</style>
</head>
<body>
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
<input type="hidden" name="idStatut" value="{idStatut}">
<input type="hidden" name="filtre" value="{filtre}">
</form>
</body>
</html>
