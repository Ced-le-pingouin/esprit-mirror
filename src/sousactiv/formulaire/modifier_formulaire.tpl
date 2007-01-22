<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Activité en ligne</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<style type="text/css">
<!--
form
{
	margin-left: {sLargeur};
	margin-right: {sLargeur};
	margin-bottom: 30px;
	margin-top: 3px;
}
input, select
{
	margin: 0 5px;
}
.p  { line-height: 10.5pt; font-family: Arial,sans-serif; font-size: 10pt; color: black; margin-top: 6px; margin-bottom: 6px; }
.InterER  { margin-top: {iInterEnonRep}px; }
.InterObj  { margin-top: {iInterElem}px; }
.titre { font-size: 1.4em; font-weight: bold; }
#barremenu
{
	margin: 0;
	padding: 3px 0;
	text-align: right;
	border-top: rgb(0,0,0) solid 1px;
	background-color: rgb(174,165,138);
	position: fixed;
	bottom: 0;
	width: 100%;
}
a:link, a:active, a:visited
{
	font-size: 11px;
	color: rgb(255,255,255);
	text-decoration: none;
	font-weight: bold;
}
a:hover
{
	color: rgb(255,255,255);
	text-decoration: underline;
}
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
</form>
{score}
<div id="barremenu">
{bouton_valider}{bouton_fermer}&nbsp;
</div>
[BLOCK_FORMULAIRE-]
[BLOCK_FERMER+]
<script language="javascript" type="text/javascript">
	top.opener.location = top.opener.location;
	top.close();
</script>
[BLOCK_FERMER-]
</body>
</html>
