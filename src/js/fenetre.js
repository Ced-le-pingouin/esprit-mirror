function ret_largeur_fenetre() {
	if (typeof(document.body) != "undefined")
		return document.body.clientWidth;
	else
		return window.innerWidth;
}

function ret_hauteur_fenetre() {
	if (typeof(document.body) != "undefined")
		return document.body.clientHeight;
	else
		return window.innerHeight;
}
