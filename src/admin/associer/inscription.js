function rechargerListeInscrits(v_iNouveauStatut) {
	with (document.getElementsByName("recharger_liste_inscrits").item(0)) {
		elements["statut"].value = v_iNouveauStatut;
		submit();
	}
	rechargerListeCours(v_iNouveauStatut);
}

function rechargerListeCours(v_iNouveauStatut) {
	with (document.getElementsByName("recharger_liste_cours").item(0)) {
		elements["statut"].value = v_iNouveauStatut;
		submit();
	}
}

function changerTaille(v_element, v_nouvelleTaille) {
	v_element.style.height = v_nouvelleTaille + "px";
}

function verifierTailles() {

	var heightBody;
	var height;
	var elem;
	var elemBody = document.getElementsByTagName('body').item(0);
	var elemTable = document.getElementsByTagName('table').item(0);

	heightTr = document.getElementsByTagName('TR').item(0).clientHeight;
	heightBody = elemBody.clientHeight - 30;

	// Changer la hauteur de la iframe de la liste des personnes
	var asIFRAMES = new Array("liste_personnes", "liste_inscrits");

	for (var i=0; i<asIFRAMES.length; i++) {
		elem = document.getElementById(asIFRAMES[i]);
		height = heightBody - heightTr;

		// Donner une hauteur minimale
		if (height < 50)
			height = 50;

		changerTaille(elem, height);
	}
}

function init() {
	verifierTailles();
}

window.onload = init;
window.onresize = verifierTailles;