<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">

<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript">
<!--
function valider() { document.forms[0].submit(); }
function init()
{
{fonction->init->corp}
}
//-->
</script>
</head>
<body onload="init()">
{evaluation->corp}
</body>
</html>

[SET_MESSAGE_ENREGISTRER+]
<p>&nbsp;</p><p style="text-align: center; font-weight: bold;">L'&eacute;valuation a bien &eacute;t&eacute; enregistr&eacute;.</p>
[SET_MESSAGE_ENREGISTRER-]

[SET_MESSAGE_PAS_EVALUE+]
<p>&nbsp;</p><p style="text-align: center; font-weight: bold;">Ce tuteur n'a pas encore &eacute;valu&eacute; ce formulaire</p>
[SET_MESSAGE_PAS_EVALUE-]

[SET_EVALUATION_TUTEUR+]
<form action="{form->action}" method="post">
<table border="0" cellspacing="0" cellpadding="3" width="450">
<tr>
<td colspan="2">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr><td style="text-align: right; width: 99%;"><div class="intitule" style="text-align: right;">&Eacute;tat du document&nbsp;:</div></td><td>{etat->liste}</td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="background-color: rgb(251,249,238); border: rgb(202,195,177) none 1px; border-top-style: dashed; border-bottom-style: dashed; text-align: center;">&nbsp;Evaluation par {tuteur->nom_complet} ({formulaire_eval->date})&nbsp;</td></tr>
<tr><td width="1%"><div class="intitule" style="text-align: right;">Appr&eacute;ciation&nbsp;:</div></td><td><input type="text" name="{appreciation->input->name}" value="{appreciation->texte}" style="width: 100%;"></td></tr>
<tr><td class="intitule" width="1%"><div class="intitule" style="text-align: right;">Commentaire&nbsp;:</div></td><td><textarea name="{commentaire->textarea->name}" style="width: 100%; height: 270px;">{commentaire->texte}</textarea></td></tr>
</table>
<input type="hidden" name="idFCSousActiv" value="{formulaire_eval->id}">
<input type="hidden" name="evalFC" value="{personne->peutEvaluer}">
</form>
[SET_EVALUATION_TUTEUR-]

[SET_EVALUATION_ETUDIANT+]
<table border="0" cellspacing="0" cellpadding="3" width="450">
<tr>
<td colspan="2">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr><td style="text-align: right; width: 99%;"><div class="intitule">&Eacute;tat du document&nbsp;:</div></td><td><div style="background-color: rgb(253,253,253); border: rgb(127,157,185) solid 1px; padding: 2px;">&nbsp;{etat->liste}&nbsp;</div></td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="background-color: rgb(251,249,238); border: rgb(202,195,177) none 1px; border-top-style: dashed; border-bottom-style: dashed; text-align: center;">&nbsp;Evaluation par {tuteur->nom_complet} ({formulaire_eval->date})&nbsp;</td></tr>
<tr><td width="1%"><div class="intitule" style="text-align: right;">Appr&eacute;ciation&nbsp;:</div></td><td><div style="padding: 3px; border: rgb(202,195,177) none 1px; border-bottom-style: dashed;">{appreciation->texte}</div></td></tr>
<tr><td class="intitule" width="1%"><div class="intitule" style="text-align: right;">Commentaire&nbsp;:</div></td><td><div style="padding: 3px; border: rgb(202,195,177) none 1px; border-bottom-style: dashed;">{commentaire->texte}</div></td></tr>
</table>
[SET_EVALUATION_ETUDIANT-]

