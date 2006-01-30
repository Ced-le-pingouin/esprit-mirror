<div id="{div->id}" class="cacher">
<fieldset>
<legend>&nbsp;Chat&nbsp;&nbsp;&nbsp;[&nbsp;{chat->url}&nbsp;]&nbsp;</legend>
<br>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><img src="theme://espacer.gif" width="20" height="1" border="0"></td>
<td class="intitule">Liste&nbsp;des&nbsp;chats&nbsp;:</td>
<td>[BLOCK_CHAT+]&nbsp;<span style="background-color: {chat->couleur->valeur}; border: rgb(0,0,0) solid 1px; text-align: center; padding: 1px; margin: 2px;" title="Couleur: {chat->couleur->nom}"><img src="theme://espacer.gif" width="8" height="6" border="0"></span>{chat->nom}<br>[BLOCK_CHAT-]</td>
</tr>
<tr>
</tr>
</table>
</fieldset>
</div>
[SET_LIEN_CHAT_ACTIF+]<a href="javascript: composer_chats('{sousactiv->id}','{sousactiv->type}');">Modifier</a>[SET_LIEN_CHAT_ACTIF-]
[SET_LIEN_CHAT_PASSIF+]<span class="lien_passif">Modifier</span>[SET_LIEN_CHAT_PASSIF-]
