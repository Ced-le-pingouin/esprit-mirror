<!--
  -- Template ...............: admin_modif.inc.tpl
  -- Description ............: 
  -- Date de création .......: 02/07/2004
  -- Dernière modification ..: 02/07/2004
  -- Auteurs ................: Filippo PORCO
  -- Emails .................: ute@umh.ac.be
  --
  -->

[SET_ECONCEPT_NUMERO_ORDRE+]
<!-- Numéro d'ordre -->
<tr>
<td nowrap="nowrap" width="1%"><div  class="intitule">Num&eacute;ro d'ordre&nbsp;:</div></td>
<td>
<select name="{select['name']}">
[BLOCK_ECONCEPT_NUMERO_ORDRE+]<option value="{option['value']}">{option['label']}</option>[BLOCK_ECONCEPT_NUMERO_ORDRE-]
</select>
</td>
</tr>
[SET_ECONCEPT_NUMERO_ORDRE+]

[SET_ECONCEPT_NOM+]
<!-- Nom -->
<tr>
<td><div class="intitule">Nom&nbsp;:</div></td>
<td><input type="text" name="{input['name']}" size="{input['size']}" value="{input['value']}"></td>
</tr>
[SET_ECONCEPT_NOM-]

[SET_ECONCEPT_STATUTS+]
<!-- Statut -->
<tr>
<td><div class="intitule">Statut&nbsp;:</div></td>
<td>
<select name="statut_formation">
[BLOCK_ECONCEPT_STATUT+]<option value="{option['value']}">{option['label']}</option>[BLOCK_ECONCEPT_STATUT-]
</select>
</td>
</tr>
[SET_ECONCEPT_STATUTS-]

[SET_ECONCEPT_TYPES+]
<!-- Type -->
<tr>
<td><div class="intitule">Type&nbsp;:&nbsp;</div></td>
<td>
<select name="{select['name']}">
[BLOCK_ECONCEPT_TYPE+]<option value="{option['value']}">{option['label']}</option>[BLOCK_ECONCEPT_TYPE+]
</select>
</td>
</tr>
[SET_ECONCEPT_TYPES-]

[SET_ECONCEPT_INTITULES+]
<tr>
<td class="intitule">Intitul&eacute;&nbsp;:</td>
<td>
<select name="{select['name']}" size="1">
<option value="0">Pas d'intitul&eacute;</option>
[BLOCK_ECONCEPT_TYPE+]<option value="{option['value']}">{option['label']}</option>[BLOCK_ECONCEPT_TYPE+]
</select>&nbsp;
<input name="{input['name']}" size="3" maxlength="3" value="{input['value']}">&nbsp;[&nbsp;<a href="javascript: ouvrir_dico_intitules('{a['href']}'); void(0);">Ajouter</a>&nbsp;]
</td>
</tr>
[SET_ECONCEPT_INTITULES-]
