<html>
<head>
<TITLE>Ajouter un élément</TITLE>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">
</head>


<body class="popup" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">

<TABLE border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td align="center" valign="middle">
		<form action=formulaire_modif_ajout.php name="formajout" method ="GET">
		<SELECT NAME="idtypeobj">
		[BLOCK_MODIF_AJOUT+]
		<OPTION VALUE="{id_type_obj}">{desc_type_obj}
		[BLOCK_MODIF_AJOUT-]
		</SELECT>
		
		<INPUT TYPE="hidden" name="idformulaire" value="{id_formulaire}">
		<INPUT TYPE="hidden" VALUE="ajouter" name="ajouter">
		</FORM>
</td></tr>
<tr><td valign="bottom">
	<table BGCOLOR="CAC3B1" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid black; padding: 3px 0px 3px 0px;"><TR width="100%">
		<td align="left">
			&nbsp
			<a href="#" onClick="document.forms['formajout'].submit();">Valider</a>
		</td>
	    <td align="right">
			<a href="#" onClick="window.close ();">Fermer</a>
			&nbsp
		</td>
	</TR></table>
</td></tr>
</table>

</body>
















<body class="popup">
<div align="center">

<div align="center">
<form action= formulaire_modif_ajout.php target="FORMFRAMEMODIF" method ="GET">
<SELECT NAME="idtypeobj">
[BLOCK_MODIF_AJOUT+]
<OPTION VALUE="{id_type_obj}">{desc_type_obj}
[BLOCK_MODIF_AJOUT-]
</SELECT>

<INPUT TYPE="hidden" name="idformulaire" value="{id_formulaire}">
<INPUT TYPE="submit" VALUE="Ajouter" name="ajouter">
</FORM>
</div>

</body>
</html>
