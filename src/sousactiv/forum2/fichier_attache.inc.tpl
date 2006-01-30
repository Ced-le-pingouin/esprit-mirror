[SET_FICHIER_ATTACHE+]
<fieldset>
<legend>&nbsp;Fichier attach&eacute;&nbsp;</legend>
<input type="file" name="{input['file']->name}" size="70" style="width: 100%;">
[BLOCK_EFFACER_FICHIER_ATTACHE+]
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;&nbsp;</td>
<td><input type="checkbox" name="{input['checkbox']->name}" onfocus="blur()"></td>
<td>&nbsp;&nbsp;</td>
<td><small>Effacer le fichier attach&eacute; &laquo;&nbsp;{fichier_attache->nom}&nbsp;&raquo;</small></td>
</tr>
</table>
[BLOCK_EFFACER_FICHIER_ATTACHE-]
</fieldset>
[SET_FICHIER_ATTACHE-]

