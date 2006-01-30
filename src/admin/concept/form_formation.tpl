<form name="form_admin_modif" action="{forms['form_admin_modif']['action']}" method="post">
<table border="0" cellpadding="5" cellspacing="2" width="100%">
<tr>
<td class="admin_modif_menu"><div style="font-weight: bold; text-align: left;">{formation->nom}</div></td>
<td class="admin_modif_menu"  width="1%" nowrap="1">...</td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<!-- Numéro d'ordre -->
<tr>
<td nowrap="nowrap" width="1%"><div  class="intitule">Num&eacute;ro d'ordre&nbsp;:</div></td>
<td>
<select name="ordre_formation">
<option value="1" selected>&nbsp;&nbsp;1&nbsp;&nbsp;</option>
<option value="2">&nbsp;&nbsp;2&nbsp;&nbsp;</option>
<option value="3">&nbsp;&nbsp;3&nbsp;&nbsp;</option>
<option value="4">&nbsp;&nbsp;4&nbsp;&nbsp;</option>
</select>
</td>
</tr>
<!-- Nom -->
<tr>
<td><div class="intitule">Nom&nbsp;:</div></td>
<td><input type="text" name="nom_formation" size="53" value="Module 1 : Loi provinciale et Finances provinciales (f&eacute;vrier 2004)"></td>
</tr>
<!-- Statut -->
<tr>
<td><div class="intitule">Statut&nbsp;:</div></td>
<td>
<select name="statut_formation">
	<option value="1">Fermé</option>
	<option value="2" selected>Ouvert</option>
	<option value="3">Invisible</option>
	<option value="4">Archivé</option>
</select>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>
<fieldset>
<legend>&nbsp;Formation&nbsp;</legend>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
<!-- Description -->
<tr>
<td class="intitule">Description&nbsp;:&nbsp;</td>
<td><span style="text-align: right;"><textarea name="descr_formation" cols="55" rows="5">{formation->description}</textarea>&nbsp;&nbsp;[&nbsp;<a href="javascript: editeur('form_admin_modif','descr_formation','{formation->nom->encoder}'); void(0);" onfocus="blur()">Editeur</a>&nbsp;]</span></td>
</tr>
<tr><td colspan="2"><span class="intitule">&nbsp;Modalité d'inscription des étudiants aux cours&nbsp;:&nbsp;</span></td></tr>
</table>

<div style="padding-left: 20px;">
<input type="radio" name="INSCR_AUTO_MODULES" value="1">&nbsp;&nbsp;Tous les étudiants participent à tous les cours
<br>
<input type="radio" name="INSCR_AUTO_MODULES" value="0" checked>&nbsp;&nbsp;Les responsables DOIVENT inscrire les étudiants qui le désirent à chaque cours
</div>
</fieldset>
</td>
</tr>
<tr>
<td align="right"><input name="VISITEUR_AUTORISER" type="checkbox" checked></td>
<td>Les visiteurs sont autoris&eacute;s &agrave; d&eacute;couvrir cette formation</td>
</tr>
</table>
<input type="hidden" name="act" value="modifier">
<input type="hidden" name="type" value="{inputs['type']['value']}">
<input type="hidden" name="params" value="{inputs['params']['value']}">
</form>

