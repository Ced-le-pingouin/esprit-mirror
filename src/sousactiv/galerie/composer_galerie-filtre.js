function init() {
	document.getElementsByName("personne").item(0).onchange = function() { envoyer(); };
	document.getElementsByName("document").item(0).onchange = function() { envoyer(); };
	document.getElementsByName("collecticiel").item(0).onchange = function() { envoyer(); };
}

function envoyer() { document.forms[0].submit(); }

window.onload = init;
