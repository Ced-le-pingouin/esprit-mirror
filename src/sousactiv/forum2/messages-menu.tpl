<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://dialog.css">
<link type="text/css" rel="stylesheet" href="theme://dialogue/dialog-sous_menu.css">
<link type="text/css" rel="stylesheet" href="theme://forum/messages-menu.css">
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="forum.js"></script>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="18">
<tr>
{menu_messages}
</tr>
</table>
</body>
</html>
[SET_MENU_AJOUTER_EQUIPES+]<td class="nouveau_message_equipes" nowrap="nowrap">&nbsp;<a href="javascript: void(0);" class="ajouter" onclick="return popup_nouveau_message('0',true)" onfocus="blur()">Nouveau message (toutes les &eacute;quipes)</a>&nbsp;</td>[SET_MENU_AJOUTER_EQUIPES-]
[SET_MENU_AJOUTER+]<td class="nouveau_message" nowrap="nowrap">&nbsp;&raquo;&nbsp;<a href="javascript: void(0);" class="nouveau_message" onclick="return popup_nouveau_message('{message->equipe->id}',false)" onfocus="blur()">Nouveau message</a>&nbsp;&laquo;&nbsp;</td>[SET_MENU_AJOUTER-]
[SET_MENU_MODIFIER+]<td class="modifier_message">&nbsp;<a href="javascript: void(0);" class="modifier_message" onclick="return popup_modifier_message()" onfocus="blur()">Modifier</a>&nbsp;</td>[SET_MENU_MODIFIER-]
[SET_MENU_SUPPRIMER+]<td class="supprimer_message">&nbsp;<a href="javascript: void(0);" class="supprimer_message" onclick="return popup_supprimer_message()" onfocus="blur()">Supprimer</a>&nbsp;</td>[SET_MENU_SUPPRIMER-]
[SET_MENU_SEPARATEUR+][SET_MENU_SEPARATEUR-]
[SET_SANS_MENU+]<td>&nbsp;</td>[SET_SANS_MENU-]
