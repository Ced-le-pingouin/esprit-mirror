<html>
<head>
<TITLE>Gestion des Axes/Tendances</TITLE>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">
</head>
<body class="popup">
<h1 style="font-size:12pt; color: #777777; font-family: Verdana, Tahoma, Arial, Bitstream Vera Sans, Time;" align="center"><u>Gestion des axes</u></h1>

<FORM ACTION="gestion_axes.php" name="formsup" method ="GET">

<fieldset><legend><b>Supprimer un axe</b></legend>
[BLOCK_AXES+]
<INPUT TYPE="radio" name="axe_s" value="{id_axe}">{desc_axe}<br>
[BLOCK_AXES-]


<div align="center">
<a href="#" onclick="document.forms['formsup'].submit();">Supprimer</a>
<INPUT TYPE="hidden" VALUE="Supprimer" name="supprimer">
</div>

</fieldset>

</FORM>



<FORM ACTION="gestion_axes.php" name="formmodif" method ="GET">

<fieldset><legend><b>Modifier le nom d'un axe</b></legend>
[BLOCK_AXES2+]
<INPUT TYPE="radio" name="axe_m" onclick="document.formmodif.axemodif.value = '{desc_axe2js}'; document.formmodif.axemodif.focus();" 
	value="{id_axe2}">{desc_axe2}<br>
[BLOCK_AXES2-]


<div align="center">
<INPUT TYPE="text" NAME="axemodif" SIZE="60" MAXLENGTH="100">
<a href="#" onclick="document.forms['formmodif'].submit();">Modifier</a>
<INPUT TYPE="hidden" VALUE="Modifier" name="modifier">
</div>

</fieldset>

</FORM>



<FORM ACTION="gestion_axes.php" name="formajout" method ="GET">

<fieldset><legend><b>Ajouter un axe</b></legend>

<div align="center">
<INPUT TYPE="text" NAME="axeajout" SIZE="60" MAXLENGTH="100">
<a href="#" onclick="document.forms['formajout'].submit();">Ajouter</a>
<INPUT TYPE="hidden" VALUE="Ajouter" name="ajouter">
</div>
</fieldset>

</FORM>

</body>
