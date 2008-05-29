<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript">
<!--

var id_menu_ajouter   = '0';
var id_menu_modifier  = '1';
var id_menu_supprimer = '2';

function init()
{
}

function modifier(v_iIdIntitule,v_sNomIntitule)
{
	with (document.forms[0]) {
		elements["ID_INTITULE"].value = v_iIdIntitule;
		elements["NOM_INTITULE"].value = v_sNomIntitule;
		// Dans le cas d'une modification
		elements["NOM_INTITULE_SAUVER"].value = v_sNomIntitule;
	}
}

function retIdIntitule()
{
	return document.forms[0].elements["ID_INTITULE"].value;
}

function retNomIntitule()
{
	return document.forms[0].elements["MODE"].value;
}

function envoyer(v_iMode)
{
	var bEnvoyer = false;
	var sMessage;
	
	if (typeof(document.forms[0].elements) != "undefined")
	{
		switch (v_iMode)
		{
			case id_menu_ajouter:
				sMessage = "Voulez-vous ajouter"
					+ " \"" + document.forms[0].elements["NOM_INTITULE"].value + "\""
					+ " dans la liste des intitulés ?";
				bEnvoyer = confirm(sMessage);
				break;
				
			case id_menu_modifier:
				sMessage = "Voulez-vous remplacer"
					+ "\n    \"" + document.forms[0].elements["NOM_INTITULE_SAUVER"].value + "\""
					+ "\npar"
					+ "\n    \"" + document.forms[0].elements["NOM_INTITULE"].value + "\""
					+ " ?";
				bEnvoyer = confirm(sMessage);
				break;
				
			case id_menu_supprimer:
				sMessage = "Voulez-vous supprimer"
					+ " \"" + document.forms[0].elements["NOM_INTITULE"].value + "\""
					+ " de la liste des intitulés ?";
				bEnvoyer = confirm(sMessage);
				break;
		}
		
		if (bEnvoyer)
		{
			document.forms[0].elements["MODE"].value = v_iMode;
			document.forms[0].submit();
			document.forms[0].reset();
		}
	}
}

//-->
</script>
<style type="text/css">
<!--
body { background-image: none; background-color: rgb(243,244,244); }
-->
</style>
</head>
<body onload="init()">
<div align="center">
<form action="intitule.php" target="Principale" method="get">
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td><input type="text" name="NOM_INTITULE" maxlength="255" size="40" value="" style="width: 100%;"></td></tr>
<tr><td style="text-align: right;">[BLOCK_MENU+][BLOCK_MENU-]</td></tr>
</table>
<input type="hidden" name="MODE" value="">
<input type="hidden" name="ID_INTITULE" value="{intitule->id}">
<input type="hidden" name="TYPE_INTITULE" value="{intitule->type}">
<input type="hidden" name="NOM_INTITULE_SAUVER" value="">
</form>
</div>
</body>
</html>
[SET_MENU_SEPARATEUR+]&nbsp;&nbsp;[SET_MENU_SEPARATEUR-]
[SET_MENU_AJOUTER+]<a href="javascript: envoyer(id_menu_ajouter); void(0);">Ajouter</a>[SET_MENU_AJOUTER-]
[SET_MENU_MODIFIER+]<a href="javascript: envoyer(id_menu_modifier); void(0);">Remplacer</a>[SET_MENU_MODIFIER-]
[SET_MENU_SUPPRIMER+]<a href="javascript: envoyer(id_menu_supprimer); void(0);">Supprimer</a>[SET_MENU_SUPPRIMER-]
