<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Activité en ligne</title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css" />
<style type="text/css">
<!--
form
{
	margin-bottom: 30px;
	margin-left: {sLargeur};
	margin-right: {sLargeur};
	margin-top: 20px;
}
.InterER
{
	margin-top: {iInterEnonRep}px;
}
.InterObj
{
	margin-top: {iInterElem}px;
}
-->
</style>
<script src="javascript://globals.js" type="text/javascript"></script>
<script src="{formulaire_js}" type="text/javascript"></script>
<script src="{general_js_php}" type="text/javascript"></script>
</head>
<body class="modifier_formulaire">
<div id="entete"><h3>{Nom_etudiant}{Info_ael}</h3></div>
<table {sEncadrer} align="center" class="titre">
<tr>
	<td>
		{sTitre}
	</td>
</tr>
</table>
[BLOCK_EVAL_ETAT+]
<div id="Eval">
<h3>{Eval_Globale}</h3>
<p>{txt_eval}</p>
</div>
<div id="Etat">
<h3>Etat de l'activité : </h3>
{txt_etat}
</div>
[BLOCK_EVAL_ETAT-]
<br style="clear: both;" />
<form name="questionnaire" class="formFormulaire" action="modifier_formulaire.php" method="post" enctype="text/html">
[BLOCK_FORMULAIRE+]
<input type="hidden" name="idFormulaire" value="{iIdFormulaire}" />
{input_ss_activ}
{ListeObjetFormul}
</form>
<div id="barremenu">
{bouton_fermer}{bouton_valider}
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
