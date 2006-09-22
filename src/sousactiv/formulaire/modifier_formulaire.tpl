<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Activité en ligne</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<style type="text/css">
<!--
form  { margin-left: {sLargeur}; margin-right: {sLargeur}; }
.p  { line-height: 10.5pt; font-family: Arial,sans-serif; font-size: 10pt; color: black; margin-top: 6px; margin-bottom: 6px; }
.InterER  { margin-top: {iInterEnonRep}px; }
.InterObj  { margin-top: {iInterElem}px; }
.titre { font-size: 1.4em; font-weight: bold; }
-->
</style>
<script src="{formulaire_js}" type="text/javascript"></script>
<script src="{general_js_php}" type="text/javascript"></script>
</head>
<body class="liste">
[BLOCK_FORMULAIRE+]
<form name="questionnaire" action="modifier_formulaire.php" method="post" enctype="text/html" class="formFormulaire">
<input type="hidden" name="bSoumis" value="1" />
<input type="hidden" name="idFormulaire" value="{iIdFormulaire}" />
{input_ss_activ}
<table {sEncadrer} align="center" class="titre">
<tr>
	<td>
		{sTitre}
	</td>
</tr>
</table>
<br /><br />
{ListeObjetFormul}
<div align="center">
<input type="button" value="Valider" name="soumettre" onclick="validerFormulaire({iRemplirTout});" />
</div>
</form>
[BLOCK_FORMULAIRE-]
[BLOCK_FERMER+]
<script language="javascript" type="text/javascript">
	top.opener.location = top.opener.location;
	top.close();
</script>
[BLOCK_FERMER-]
</body>
</html>
