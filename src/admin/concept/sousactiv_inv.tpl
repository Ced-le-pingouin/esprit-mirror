<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="sousactiv_inv.css">
<link type="text/css" rel="stylesheet" href="theme://dialogue.css">
<script type="text/javascript" language="javascript" src="sousactiv_inv.js"></script>
</head>
<body>
<div id="consigne">
<p>Voici la liste des &eacute;tudiants qui sont inscrits au cours. Par d&eacute;faut, tous les &eacute;tudiants inscrits voient et peuvent activer une action que vous avez cr&eacute;&eacute;e et dont le statut est ouvert. Dans la liste ci-dessous, ils sont par d&eacute;faut, tous s&eacute;lectionn&eacute;s. Si vous d&eacute;sirez que certains &eacute;tudiants n’aient pas acc&egrave;s et ne voient pas le lien de l’action en question, d&eacute;s&eacute;lectionnez-les.</p>
</div>
<form action="sousactiv_inv.php?idSousActiv={sousactiv.id}" method="post" target="_self">
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><th>&nbsp;</th><th>Nom & pr&eacute;nom des &eacute;tudiants inscrits au cours</th></tr>
[BLOCK_PERSONNE+]
<tr>
<td class="selection"><input type="checkbox" name="idPers[]" value="{personne.id}"{personne.checked}></td>
<td><span id="pers_{personne.id}">{personne.nom} {personne.prenom}</span></td>
</tr>
[SET_ELSE_PERSONNE+]<tr><td colspan="2">Aucun inscrit trouv&eacute;</td></tr>[SET_ELSE_PERSONNE-]
[BLOCK_PERSONNE-]
</table>
<input type="hidden" name="appliquer" value="1">
</form>
<div><p>&nbsp;</p></div>
</body>
</html>
