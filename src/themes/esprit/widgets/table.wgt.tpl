[SET_TABLE_WIDGET+]
<table border="0" cellpadding="2" cellspacing="1" width="100%">
[BLOCK_ENTETE+]<tr>[BLOCK_COLONNE+]<td id="id_table_entete_{entete.id}" class="cellule_sous_titre">{entete.label}</td>[BLOCK_COLONNE-]</tr>[BLOCK_ENTETE-]
[BLOCK_LIGNE+]
[VAR_COLONNE+]
<td class="cellule_clair">{colonne.label}</td>
<td class="cellule_fonce">{colonne.label}</td>
[BLOCK_COLONNE-]
<tr>[BLOCK_COLONNE+][BLOCK_COLONNE-]</tr>
[BLOCK_LIGNE-]
</table>
[SET_TABLE_WIDGET-]
