<?php
$oBdd = new CBddMySql($g_sNomServeurTransfert,$g_sNomProprietaireTransfert,$g_sMotDePasseTransfert,$url_sNomBddSrc);

$oFormation = new CFormation($oBdd,$url_iIdFormSrc);
$sNomFormation = mb_convert_encoding($oFormation->retNom(),"HTML-ENTITIES","UTF-8");
$oBdd->terminer();
?>
<table border="0" cellspacing="0" cellpadding="7" width="100%">
<tr>
<td rowspan="3" style="width: 100px; vertical-align: top;">
<script type="text/javascript" language="javascript">
<!--
top.afficher_etape();
//-->
</script>
<small>Pour lancer la copie cliquer sur &laquo;&nbsp;Confirmer&nbsp;&raquo;.</small>
</td>
</tr>
<tr>
<td>
<fieldset>
<legend>&nbsp;Confirmer la copie&nbsp;</legend>
<div style="margin: 5px;">
Dernière étape avant la copie de la formation.
<br><br>
Sélectionnez les différentes éléments que vous désirez transférer&nbsp;:
<br><br><iframe name="selectionner_elements" src="liste_elems_transferer.php" frameborder="0" marginwidth="5" marginheight="5" height="80" width="350"></iframe>
<br><br>La copie se fera à partir de la formation &laquo;&nbsp;<cite><?php echo $sNomFormation?></cite>&nbsp;&raquo; vers
&laquo;&nbsp;<cite><?php echo $url_sNomBddDst?></cite>&nbsp;&raquo;.
</div>
</td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>
<?php $bOk = TRUE; ?>