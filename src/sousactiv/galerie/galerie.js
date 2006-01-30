function init() {
	
	if (document.getElementById("composer_galerie"))
		document.getElementById("composer_galerie").onclick = function() {
			return composer_galerie(this);
		};
	
	var elems = document.getElementsByTagName("li");
	
	for (var i=0; i<elems.length; i++) {
		elems.item(i).className = "document";
		elems.item(i).onmouseover = function() { this.className = "document_surbrillance"; };
		elems.item(i).onmouseout = function() { this.className = "document"; };
	}
}

function composer_galerie(v_oThis) {
	var sUrl = v_oThis.href;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winComposerGalerie",780,580,sOptionsFenetre);
	win.focus();
	return false;
}

window.onload = init;
