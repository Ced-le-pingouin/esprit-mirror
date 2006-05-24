<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Copie d'une rubrique</title>
<link rel="stylesheet" type="text/css" href="copie_element.css" />
<script language="javascript" type="text/javascript">
<!--
function EnvoyerSrc()
{
	if(document.choixrubsrc.IdRubSrc.options[document.choixrubsrc.IdRubSrc.selectedIndex].value == 0)
	{
		alert("Pas de rubrique selectionnée");
	}
	else
	{
		document.choixrubsrc.SrcOk.value = "1";	
		document.choixrubsrc.submit();
	}
}

function EnvoyerDst()
{
	if(document.choixrubdst.OrdreRubDst.options[document.choixrubdst.OrdreRubDst.selectedIndex].value == 0)
	{
		alert("Pas de module selectionné");
	}
	else
	{
		document.choixrubdst.DstOk.value = "1";	
		document.choixrubdst.submit();
	}
}

function Menu()
{
	top.location = "copie_element.php"
}
//-->
</script>
</head>
<body>
[ETAPE_CHOIX_SRC+]
<h4>S&eacute;lectionnez la rubrique source</h4>
<form name="choixrubsrc" action="copie_rub.php" method="get">
<fieldset>
<legend>Formation source&nbsp;</legend>
<select name="IdFormSrc" onchange="this.form.submit()">
[OPTIONSFORM]
</select>
</fieldset>
<fieldset>
<legend>Module source&nbsp;</legend>
<select name="IdModSrc" onchange="this.form.submit()">
[OPTIONSMOD]
</select>
</fieldset>
<fieldset>
<legend>Rubrique source&nbsp;</legend>
<select name="IdRubSrc">
[OPTIONSRUB]
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
<form name="choixrubdst" action="copie_rub.php" method="get">
<input name="SrcOk" type="hidden" value="1" />
<input name="DstOk" type="hidden" value="0" />
<input name="IdFormSrc" type="hidden" value="[IDFORMSRC]" />
<input name="IdModSrc" type="hidden" value="[IDMODSRC]" />
<input name="IdRubSrc" type="hidden" value="[IDRUBSRC]" />
<fieldset>
<legend>Formation de destination&nbsp;</legend>
<select name="IdFormDst" onchange="this.form.submit()">
[OPTIONSFORM]
</select>
</fieldset>
<fieldset>
<legend>Module de destination&nbsp;</legend>
<select name="IdModDst" onchange="this.form.submit()">
[OPTIONSMOD]
</select>
</fieldset>
<fieldset>
<legend>Num&eacute;ro d'ordre de la rubrique de destination&nbsp;</legend>
<select name="OrdreRubDst">
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
