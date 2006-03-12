<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">
<TITLE>Déplacer un élément</TITLE>
</head>
<body class="popup" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<TABLE border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td align="center" valign="middle">
	<form action="position_objet.php" name="formposition" method ="GET" style="margin: 0px;">
	Position : 
	<SELECT NAME="ordreobj">
	[BLOCK_POSITION+]
	<OPTION VALUE="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</OPTION>
	[BLOCK_POSITION-]
	</SELECT>
	<INPUT TYPE="hidden" name="idobj" value="{id_obj}">
	<INPUT TYPE="hidden" name="idformulaire" value="{id_formulaire}">
	<INPUT TYPE="hidden" name="deplacer" value="deplacer">
	</FORM>
</td></tr>
<tr><td valign="bottom">
	<table BGCOLOR="CAC3B1" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid black; padding: 3px 0px 3px 0px;"><TR>
		<td align="left" width="50%">
			&nbsp
			<a href="#" onClick="document.forms['formposition'].submit();">Valider</a>
		</td>
	    <td align="right" width="50%">
			<a href="#" onClick="window.close();">Fermer</a>
			&nbsp
		</td>
	</TR></table>
</td></tr>
</table>

</body>
</html>
