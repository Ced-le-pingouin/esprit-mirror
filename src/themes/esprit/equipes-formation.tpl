<table border="0" cellpadding="0" cellspacing="1" width="100%">
<tr><td colspan="5">&nbsp;</td></tr>
<tr>
<td>
<table border="0" cellpadding="2" cellspacing="1" width="100%">
[BLOCK_FORMATION_OUVERT+]
<tr>
<td class="dialog_menu_intitule" width="20"><div id="id_form" align="right"><img src="theme://blank.gif" width="14" height="11" border="0"></div></td>
<td class="dialog_menu_intitule" width="1%"><input type="radio" name="Niveau" value="{id_formation}" onclick="changerComposition('{type_formation}',value,'{nom_formation_encoder}')" onfocus="blur()" checked></td>
<td class="dialog_menu_intitule" colspan="3"><span class="afficher_curseur_aide" title="{nom_formation}" onmouseover="StatusBar(this,'{nom_formation_encoder}')" onmouseout="StatusBar(null,'')"><b>Formation</b></span></td>
</tr>
[BLOCK_FORMATION_OUVERT-]
[BLOCK_FORMATION_FERMER+]
<tr>
<td class="dialog_menu_intitule" width="20"><img src="theme://blank.gif" width="14" height="11" border="0"></td>
<td class="dialog_menu_intitule">&nbsp;</td>
<td class="dialog_menu_intitule" colspan="3"><span class="afficher_curseur_aide" title="{nom_formation}" onmouseover="StatusBar(this,'{nom_formation_encoder}')" onmouseout="StatusBar(null,'')"><b>Formation</b></span></td>
</tr>
[BLOCK_FORMATION_FERMER-]
[BLOCK_MODULE+]
<tr>
<td class="dialog_menu_intitule" width="20">&nbsp;</td>
<td class="dialog_menu_intitule" width="20"><div id="id_mod_{id_module}" align="center"><img src="theme://blank.gif" width="14" height="11" border="0"></div></td>
<td class="dialog_menu_intitule" width="1%"><input type="radio" name="Niveau" value="{id_module}" onclick="changerComposition('{type_module}',value,'{nom_module_encoder}')" onfocus="blur()"{select_module}></td>
<td class="dialog_menu_intitule" colspan="2"><span class="afficher_curseur_aide" title="{nom_module}" onmouseover="StatusBar(this,'{nom_module_encoder}')" onmouseout="StatusBar(null,'')"><b>Cours&nbsp;{ordre_module}</b></span></td>
</tr>
[BLOCK_UNITE+]
<tr>
<td class="dialog_menu_intitule" width="20">&nbsp;</td>
<td class="dialog_menu_intitule" width="20">&nbsp;</td>
<td class="dialog_menu_intitule" width="20"><div id="id_rubrique_{id_unite}" align="center"><img src="theme://blank.gif" width="14" height="11" border="0"></div></td>
<td class="dialog_menu_intitule" width="1%"><input type="radio" name="Niveau" value="{id_unite}" onclick="changerComposition('{type_unite}',value,'{nom_unite_encoder}')" onfocus="blur()"></td>
<td class="dialog_menu_intitule"><span class="afficher_curseur_aide" title="{nom_unite}" onmouseover="StatusBar(this,'{nom_unite_encoder}')" onmouseout="StatusBar(null,'')">Unit&eacute;&nbsp;{ordre_unite}</span></td>
</tr>
[BLOCK_UNITE-]
[BLOCK_MODULE-]
</table>
</td>
</tr>
</table>
