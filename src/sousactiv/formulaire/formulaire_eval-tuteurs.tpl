<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://commun/dialog.css">
</head>
<body class="gauche">
<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr><td class="dialog_menu_fond">
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr><td class="dialog_menu_titre">Liste des tuteurs</td></tr>
[BLOCK_TUTEUR+]
<tr>
<td class="dialog_menu_intitule"><a href="formulaire_eval.php?idPers={personne->id}&idFCSousActiv={formulaire_complete->id}&evalFC={personne->peutEvaluer}" target="PRINCIPALE" title="Cliquer ici pour voir l'&eacute;valuation de ce tuteur" onfocus="blur()">{personne->nom_complet}</a></td>
</tr>
[BLOCK_TUTEUR-]
<tr></tr>
</table>
</td></tr>
</table></body>
</html>

