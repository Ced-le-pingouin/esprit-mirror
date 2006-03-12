<table border="0" cellspacing="0" cellpadding="7" width="100%">
<tr>
<td rowspan="3" style="width: 100px; vertical-align: top;">
<script type="text/javascript" language="javascript">
<!--
top.afficher_etape();
//-->
</script>
<small>Voici une liste de formations appartenant à la base de données source &laquo;&nbsp;<cite><?=$HTTP_GET_VARS['NOM_BDD_SRC']?></cite>&nbsp;&raquo;.</small>
<br><br>
<small>Sélectionnez la formation qui sera copiée dans &laquo;&nbsp;<cite><?=$HTTP_GET_VARS['NOM_BDD_DST']?></cite>&nbsp;&raquo;.</small>
</td>
</tr>
<tr>
<td>
<fieldset>
<legend>&nbsp;Liste des formations&nbsp;</legend>
<div style="margin: 5px; text-align: center;"><iframe name="liste_formations" src="liste_forms.php?bdd=<?=$url_sNomBddSrc?>&idFormSrc=<?=$url_iIdFormSrc?>" frameborder="0" marginwidth="0" marginheight="0" style="width: 340px; height: 220px;" scrolling="yes"></iframe></div>
</td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>
<?php $bOk = TRUE; ?>