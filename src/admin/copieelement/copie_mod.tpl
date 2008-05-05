<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Copie d'un module</title>
<link rel="stylesheet" type="text/css" href="css://admin/admin_general.css" />
<script language="javascript" type="text/javascript">
<!--
function EnvoyerSrc()
{
	if(document.choixmodsrc.IdModSrc.options[document.choixmodsrc.IdModSrc.selectedIndex].value == 0)
	{
		alert("Pas de module selectionné");
	}
	else
	{
		document.choixmodsrc.SrcOk.value = "1";	
		document.choixmodsrc.submit();
	}
}

function EnvoyerDst()
{
	document.choixmoddst.DstOk.value = "1";	
	document.choixmoddst.submit();
}

function Menu()
{
	top.location = "copie_element.php"
}
//-->
</script>
</head>
<body class="copie_element">
[ETAPE_CHOIX_SRC+]
<h4>S&eacute;lectionnez le module source</h4>
<form name="choixmodsrc" action="copie_mod.php" method="get">
<fieldset>
<legend>Formation source&nbsp;</legend>
<select name="IdFormSrc" onchange="this.form.submit()">
[OPTIONSFORM]
</select>
</fieldset>
<fieldset>
<legend>Module source&nbsp;</legend>
<select name="IdModSrc">
[OPTIONSMOD]
</select>
</fieldset>
<input name="SrcOk" type="hidden" value="0" />
<div id="navmenu">
	<a id="fermer" href="javascript: top.close();">Fermer</a>
	<a id="suivant" href="javascript: EnvoyerSrc();">Suivant</a>
</div>
</form>
[ETAPE_CHOIX_SRC-]

[ETAPE_CHOIX_DST+]
<h4>S&eacute;lectionnez l'endroit de destination</h4>
<form name="choixmoddst" action="copie_mod.php" method="get">
<input name="SrcOk" type="hidden" value="1" />
<input name="DstOk" type="hidden" value="0" />
<input name="IdFormSrc" type="hidden" value="[IDFORMSRC]" />
<input name="IdModSrc" type="hidden" value="[IDMODSRC]" />
<fieldset>
<legend>Formation de destination&nbsp;</legend>
<select name="IdFormDst" onchange="this.form.submit()">
[OPTIONSFORM]
</select>
</fieldset>
<fieldset>
<legend>Num&eacute;ro d'ordre du module de destination&nbsp;</legend>
<select name="OrdreModDst">
<option value="1">1</option>
[OPTIONSORDRE]
</select>
</fieldset>

</form>
<div id="navmenu">
	<a id="annuler" href="javascript: top.close();">Annuler</a>
	<a id="valider" href="javascript: EnvoyerDst();">Valider</a>
</div>
[ETAPE_CHOIX_DST-]
[ETAPE_FINAL+]
[LOG_FINAL]
<div id="navmenu">
	<a id="fermer" href="javascript: top.close();">Fermer</a>
	<a id="menu" href="javascript: Menu();">Menu</a>
</div>
[ETAPE_FINAL-]
</body>
</html>
