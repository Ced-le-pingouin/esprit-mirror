function oListe() { return top.frames["Liste"]; }
function oPrincipal() { return top.frames["Principal"]; }
function oSousMenu() { return top.frames["SousMenu"]; }
function AfficherSalon(v_iIdChat) {
	oPrincipal().location = "chat.php?idChat=" + v_iIdChat;
	oSousMenu().location = "chat-sous_menu.php" + (parseInt(v_iIdChat) > 0 ? "?AM=1" : "");
}
function rafraichir_parent() {
	if (top.recharger_fenetre_parente &&
		top.opener &&
		top.opener.document &&
		top.opener.document.forms.length > 0)
		top.opener.document.forms[0].submit()
}
function on_error() { return true; }
window.onerror = on_error;
